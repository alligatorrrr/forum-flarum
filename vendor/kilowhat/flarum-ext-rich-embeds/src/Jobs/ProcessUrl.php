<?php

namespace Kilowhat\RichEmbeds\Jobs;

use Carbon\Carbon;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Kilowhat\RichEmbeds\ApiResourceProcessor;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Exceptions\BlacklistedUrl;
use Kilowhat\RichEmbeds\Exceptions\BodyTooLarge;
use Kilowhat\RichEmbeds\Parser;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class ProcessUrl implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $embed;

    public function __construct(Embed $embed)
    {
        $this->embed = $embed;
    }

    public function handle(SettingsRepositoryInterface $settings, WhitelistManager $whitelist)
    {
        $client = new Client([
            'http_errors' => false,
        ]);

        // Reset in case we are re-processing a URL
        $this->embed->http_status = null;
        $this->embed->mime = null;
        $this->embed->error = null;
        $this->embed->opengraph = null;
        $this->embed->icons = null;
        $this->embed->fallback = null;
        $this->embed->exif = null;
        $this->embed->api_resource = null;

        try {
            // Process the API resource first, because this might succeed even if the crawler later fails below
            (new ApiResourceProcessor($settings, $client, $this->embed))->process();

            $finalUri = new Uri($this->embed->url);

            $response = $client->get($finalUri, [
                'on_redirect' => function (RequestInterface $request, ResponseInterface $response, UriInterface $uri) use (&$finalUri, $whitelist) {
                    if (!$whitelist->isWhitelisted($uri)) {
                        throw new BlacklistedUrl($uri);
                    }

                    // Track any redirect that might have happened
                    $finalUri = $uri;
                },
                'stream' => true,
            ]);

            $this->embed->final_url = (string)$finalUri;
            $this->embed->http_status = $response->getStatusCode();
            $this->embed->mime = Str::limit($response->getHeaderLine('Content-Type'), Embed::$mimeStringLength, '');

            $body = $response->getBody();
            $bytesRead = 0;
            $contents = '';

            // Don't download the whole file if it's too big
            while (!$body->eof()) {
                $data = $body->read(1024);
                $contents .= $data;
                $bytesRead += strlen($data);
                if ($bytesRead >= 5 * 1024 * 1024) { // 5MB limit
                    $body->close();
                    throw new BodyTooLarge('Body too large');
                }
            }

            $this->embed->size = $bytesRead;

            if (
                $response->getStatusCode() >= 200 &&
                $response->getStatusCode() < 300
            ) {
                // HTML MIME will commonly be like "text/html" or "text/html; charset=utf-8"
                if (Str::startsWith($this->embed->mime, 'text/html')) {
                    $parser = new Parser();

                    $parser->load($contents, $finalUri);

                    $opengraph = $parser->opengraph();

                    if (count($opengraph)) {
                        $this->embed->opengraph = $opengraph;
                    }

                    $icons = $parser->icons();

                    if (count($icons)) {
                        $this->embed->icons = $icons;
                    }

                    $fallback = $parser->fallback();

                    if (count($fallback)) {
                        $this->embed->fallback = $fallback;
                    }

                    // The "Flarum" API data doesn't need to be queried separately, it's already in the HTML
                    if ($response->getHeaderLine('X-Powered-By') === 'Flarum' && $settings->get('kilowhat-rich-embeds.flarumApiPreviews')) {
                        $flarumApi = $parser->flarumApiPayload();

                        if ($flarumApi) {
                            $this->embed->api_resource = $flarumApi;
                        }
                    }
                } else if (Str::startsWith($this->embed->mime, 'image/')) {
                    // We don't really care if the image manager will throw an error for invalid file here
                    // It will be caught by the try/catch block that wraps all the parsers
                    // There doesn't appear to be any way to provide binary data directly to Intervention without the risk
                    // of evaluating the variable as a URL or local filepath
                    // To ensure the untrusted content is not evaluated, we save it to a temporary file
                    $tmpFile = tempnam('/tmp', 'kilowhat-rich-embed-');
                    file_put_contents($tmpFile, $contents);
                    try {
                        $image = (new ImageManager())->make($tmpFile);
                    } finally {
                        unlink($tmpFile);
                    }

                    $this->embed->width = $image->width();
                    $this->embed->height = $image->height();
                    $this->embed->exif = $image->exif();
                }
            }
        } catch (\Exception $exception) {
            $this->embed->error = get_class($exception);
        }

        $this->embed->retrieved_at = Carbon::now();
        $this->embed->save();
    }
}

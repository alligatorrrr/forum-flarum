<?php

namespace Kilowhat\RichEmbeds\Controllers;

use enshrined\svgSanitize\Sanitizer;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ImageProxyController implements RequestHandlerInterface
{
    protected $settings;
    protected $whitelist;

    public function __construct(SettingsRepositoryInterface $settings, WhitelistManager $whitelist)
    {
        $this->settings = $settings;
        $this->whitelist = $whitelist;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->settings->get('kilowhat-rich-embeds.imageProxy')) {
            return $this->errorImageResponse('Proxy', 'disabled');
        }

        $url = urldecode((string)Arr::get($request->getQueryParams(), 'url'));

        try {
            $this->whitelist->assertValidUrl($url);
            $this->whitelist->assertWhitelisted($url);
        } catch (\Exception $exception) {
            return $this->errorImageResponse('Invalid', 'image URL');
        }

        $client = new Client([
            'http_errors' => false,
            'stream' => true,
        ]);

        $response = $client->get($url);

        // Handle upstream errors here and don't report them to the Flarum log file
        if ($response->getStatusCode() !== 200) {
            return $this->errorImageResponse('Image download', 'error: ' . $response->getStatusCode());
        }

        $type = explode('/', $response->getHeaderLine('Content-Type'));
        $body = $response->getBody();

        if (
            count($type) !== 2 || $type[0] !== 'image' ||
            // List of image types based on https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
            // This is the second part of the MIME type, not the file extension
            !in_array($type[1], [
                'apng', 'avif', 'gif', 'jpeg', 'png', 'svg+xml', 'webp',
                'bmp', 'vnd.microsoft.icon', 'x-icon', 'tiff',
            ])) {
            return $this->errorImageResponse('Unsupported image', 'MIME: ' . ($response->getHeaderLine('Content-Type') ?: 'missing'));
        }

        $bytesRead = 0;
        $contents = '';

        // Don't download the whole file if it's too big
        while (!$body->eof()) {
            $data = $body->read(1024);
            $contents .= $data;
            $bytesRead += strlen($data);
            if ($bytesRead >= 5 * 1024 * 1024) { // 5MB limit
                $body->close();
                return $this->errorImageResponse('Image', 'too large');
            }
        }

        // Protect against XSS through SVG direct access
        if ($type[1] === 'svg+xml') {
            $sanitizer = new Sanitizer();
            $sanitizer->removeRemoteReferences(true);

            return new TextResponse(
                $sanitizer->sanitize($contents),
                200,
                [
                    'Content-Type' => 'image/svg+xml',
                ]
            );
        }

        return new TextResponse(
            $contents,
            200,
            [
                'Content-Type' => $type,
            ]
        );
    }

    protected function errorImageResponse(string $message, string $message2): TextResponse
    {
        $xmlMessage = htmlspecialchars($message, ENT_XML1);
        $xmlMessage2 = htmlspecialchars($message2, ENT_XML1);

        return new TextResponse(
            <<<SVG
<?xml version="1.0"?>
<svg xmlns="http://www.w3.org/2000/svg" width="100px" height="100px" viewBox="0 0 100 100">
<path d="M 37 27 l 26 26 m -26 0 l 26 -26" stroke="#f00" stroke-width="6"/>
<text x="50" y="74" font-size="10" text-anchor="middle">$xmlMessage</text>
<text x="50" y="86" font-size="10" text-anchor="middle">$xmlMessage2</text>
</svg>
SVG
            ,
            200,
            [
                'Content-Type' => 'image/svg+xml',
            ]
        );
    }
}

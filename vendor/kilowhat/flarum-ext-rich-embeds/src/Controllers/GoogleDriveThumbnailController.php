<?php

namespace Kilowhat\RichEmbeds\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Google\Service\Drive;
use Google\Service\Exception as ServiceException;
use GuzzleHttp\Psr7\BufferStream;
use Illuminate\Support\Arr;
use Kilowhat\RichEmbeds\ExceptionLoggerTrait;
use Kilowhat\RichEmbeds\GoogleClientTrait;
use Kilowhat\RichEmbeds\GoogleDriveThumbnailRepository;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GoogleDriveThumbnailController implements RequestHandlerInterface
{
    use ExceptionLoggerTrait;
    use GoogleClientTrait;

    protected $cache;
    protected $settings;

    public function __construct(GoogleDriveThumbnailRepository $cache, SettingsRepositoryInterface $settings)
    {
        $this->cache = $cache;
        $this->settings = $settings;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        RequestUtil::getActor($request)->assertCan('kilowhat-rich-embeds.viewGoogleDriveThumbnails');

        $id = Arr::get($request->getQueryParams(), 'id');

        $thumbnail = $this->cache->get($id);

        if ($thumbnail) {
            return $this->createThumbnailResponse($thumbnail);
        }

        $service = new Drive($this->createGoogleApiClient($this->settings, [Drive::DRIVE_READONLY]));

        try {
            $file = $service->files->get($id, [
                'fields' => 'thumbnailLink',
            ]);
        } catch (\Exception $exception) {
            if ($exception instanceof ServiceException && $exception->getCode() === 404) {
                return $this->createErrorResponse();
            }

            $this->logError("Google Drive API error: looking for ID $id threw", $exception);

            return $this->createErrorResponse();
        }

        $thumbnail = $this->cache->retrieve($file);

        if (!$thumbnail) {
            return $this->createErrorResponse();
        }

        return $this->createThumbnailResponse($thumbnail);
    }

    protected function createThumbnailResponse(array $thumbnail): Response
    {
        // We need to create our own buffer because the default fails on NULL bytes in the image
        $buffer = new BufferStream();
        $buffer->write($thumbnail[1]);

        return new Response($buffer, 200, [
            'Content-Type' => $thumbnail[0] ?: 'image/jpeg',
        ]);
    }

    protected function createErrorResponse(): Response
    {
        return new Response\TextResponse('Could not retrieve thumbnail', 400);
    }
}

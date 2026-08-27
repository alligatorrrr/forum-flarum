<?php

namespace Kilowhat\RichEmbeds;

use Google\Service\Drive\DriveFile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Cache\Repository;

class GoogleDriveThumbnailRepository
{
    use ExceptionLoggerTrait;

    protected $cache;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function get(string $id): ?array
    {
        return $this->cache->get('rich-embeds-google-drive-thumbnail-' . $id);
    }

    public function retrieve(DriveFile $file): ?array
    {
        if (!$file->thumbnailLink) {
            return null;
        }

        try {
            $client = new Client();

            $response = $client->get($file->thumbnailLink);
        } catch (TransferException $exception) {
            if ($exception instanceof BadResponseException && $exception->getResponse()->getStatusCode() === 404) {
                return null;
            }

            $this->logError("Google Drive error downloading thumbnail for file {$file->id}:", $exception);

            return null;
        }

        $value = [
            $response->getHeaderLine('Content-Type'),
            $response->getBody()->getContents(),
        ];

        $this->cache->forever('rich-embeds-google-drive-thumbnail-' . $file->id, $value);

        return $value;
    }
}

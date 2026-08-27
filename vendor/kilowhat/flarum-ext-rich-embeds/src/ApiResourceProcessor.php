<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Settings\SettingsRepositoryInterface;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Exception as ServiceException;
use Google\Service\YouTube;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Utils;
use Psr\Log\LoggerInterface;

class ApiResourceProcessor
{
    use ExceptionLoggerTrait;
    use GoogleClientTrait;

    protected $settings;
    protected $client;
    protected $embed;

    public function __construct(SettingsRepositoryInterface $settings, Client $client, Embed $embed)
    {
        $this->settings = $settings;
        $this->client = $client;
        $this->embed = $embed;
    }

    public function process(): void
    {
        $uri = new Uri($this->embed->url);

        if ($uri->getHost() === 'github.com' || $uri->getHost() === 'www.github.com') {
            if (!$this->settings->get('kilowhat-rich-embeds.githubApiKey')) {
                return;
            }

            if (preg_match('~^/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)(?:/(issues|pulls?)/([0-9]+))?$~', $uri->getPath(), $matches) !== 1) {
                return;
            }

            list(, $owner, $repo) = $matches;

            if ($orgWhitelist = trim($this->settings->get('kilowhat-rich-embeds.githubOrgWhitelist'), " \t\n\r\0\x0B,")) {
                if (!in_array($owner, explode(',', $orgWhitelist))) {
                    return;
                }
            }

            $payload = [];
            $apiType = null;

            // If it's an issue or PR, there will be 5 items in the array
            if (count($matches) > 3) {
                list(, , , $type, $number) = $matches;

                // Our regex above will match both "pull" and "pulls" because it's unclear if older URLs used plural. But API uses plural
                $apiType = $type === 'pull' ? 'pulls' : $type;

                $response = $this->githubGet("repos/$owner/$repo/$apiType/$number");

                if ($response) {
                    $payload['github.' . ($apiType === 'issues' ? 'issue' : 'pull')] = $response;
                }
            }

            // Pulls don't need an additional request for the repo, because it's already included in base.repo
            if ($apiType !== 'pulls') {
                $response = $this->githubGet("repos/$owner/$repo");

                if ($response) {
                    $payload['github.repo'] = $response;
                }
            }

            if (count($payload)) {
                $this->embed->api_resource = $payload;
            }

            return;
        }

        if ($uri->getHost() === 'drive.google.com') {
            if (!$this->settings->get('kilowhat-rich-embeds.googledriveApiPreviews')) {
                return;
            }

            if (preg_match('~^/file/d/([A-Za-z0-9_-]+)/view~', $uri->getPath(), $matches) !== 1) {
                return;
            }

            $response = $this->googleDriveGet($matches[1]);

            if ($response) {
                $this->embed->api_resource = [
                    'googledrive.file' => $response,
                ];
            }
        }

        if ($uri->getHost() === 'youtube.com' || $uri->getHost() === 'www.youtube.com') {
            if (!$this->settings->get('kilowhat-rich-embeds.youtubeApiPreviews')) {
                return;
            }

            // regex from https://github.com/s9e/TextFormatter/blob/master/src/Plugins/MediaEmbed/Configurator/sites/youtube.xml
            if (preg_match('~^/(?:watch.*?v=|shorts/|v/|attribution_link.*?v%3D)([-\w]+)~', $uri->getPath(), $matches) !== 1) {
                return;
            }

            $response = $this->youtubeVideoGet($matches[1]);

            if ($response) {
                $this->embed->api_resource = [
                    'youtube.video' => $response,
                ];
            }
        }

        if ($uri->getHost() === 'youtu.be') {
            if (!$this->settings->get('kilowhat-rich-embeds.youtubeApiPreviews')) {
                return;
            }

            if (preg_match('~^/([-\w]+)~', $uri->getPath(), $matches) !== 1) {
                return;
            }

            $response = $this->youtubeVideoGet($matches[1]);

            if ($response) {
                $this->embed->api_resource = [
                    'youtube.video' => $response,
                ];
            }
        }
    }

    protected function githubGet(string $path)
    {
        $uri = "https://api.github.com/$path";

        $response = $this->client->get($uri, [
            'Headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'token ' . $this->settings->get('kilowhat-rich-embeds.githubApiKey'),
            ],
        ]);

        switch ($response->getStatusCode()) {
            case 200:
                return Utils::jsonDecode($response->getBody()->getContents());
            case 301: // We won't follow redirects for now
            case 404:
                return null;
        }

        resolve(LoggerInterface::class)->error("[rich-embeds] GitHub API error: $uri returned HTTP {$response->getStatusCode()}");

        return null;
    }

    protected function googleApiClient(): GoogleClient
    {
        try {
            return $this->createGoogleApiClient($this->settings, [
                Drive::DRIVE_READONLY,
                YouTube::YOUTUBE_READONLY,
            ]);
        } catch (\Exception $exception) {
            $this->logError("Google SDK initialisation error:", $exception);

            // If there's a problem at this point, it makes sense to crash the whole thing so that it's clear something is broken
            throw $exception;
        }
    }

    protected function googleDriveGet(string $id)
    {
        $service = new Drive($this->googleApiClient());

        try {
            // https://developers.google.com/drive/api/v3/reference/files
            $response = $service->files->get($id, [
                'fields' => 'id,name,mimeType,description,webViewLink,iconLink,thumbnailLink,createdTime,modifiedTime,size',
            ]);
        } catch (\Exception $exception) {
            if ($exception instanceof ServiceException && $exception->getCode() === 404) {
                return null;
            }

            $this->logError("Google Drive API error: looking for ID $id threw", $exception);

            return null;
        }

        $cache = resolve(GoogleDriveThumbnailRepository::class);
        $cache->retrieve($response);

        return $response->toSimpleObject();
    }


    protected function youtubeVideoGet(string $id)
    {
        $service = new YouTube($this->googleApiClient());

        try {
            // https://developers.google.com/youtube/v3/docs/videos/list
            // https://developers.google.com/youtube/v3/docs/videos#resource
            $response = $service->videos->listVideos('snippet,contentDetails,statistics', [
                'id' => $id,
            ]);
        } catch (\Exception $exception) {
            $this->logError("Google YouTube API error: looking for ID $id threw", $exception);

            return null;
        }

        if ($response->count()) {
            return $response->getItems()[0]->toSimpleObject();
        }

        // No video matches ID
        return null;
    }
}

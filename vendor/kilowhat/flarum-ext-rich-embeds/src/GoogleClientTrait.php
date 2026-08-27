<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Settings\SettingsRepositoryInterface;
use Google\Client;
use GuzzleHttp\Utils;
use Illuminate\Support\Str;

trait GoogleClientTrait
{
    /**
     * Creates the client for use in the API resource processor and the image cache controller
     * We cannot use a container binding because we need the ability to customize the scopes
     * And we don't want it to conflict with other extension that also use it
     * @param SettingsRepositoryInterface $settings
     * @param array $scopes
     * @return Client
     * @throws \Google\Exception
     */
    protected function createGoogleApiClient(SettingsRepositoryInterface $settings, array $scopes): Client
    {
        $client = new Client();
        $client->setApplicationName('Flarum');

        if ($key = $settings->get('kilowhat-rich-embeds.googleApiKey')) {
            $client->setDeveloperKey($key);
        } else if ($config = trim($settings->get('kilowhat-rich-embeds.googleApiAuthConfig'))) {
            if (Str::startsWith($config, '{')) {
                $config = Utils::jsonDecode($config, true);
            }

            $client->setAuthConfig($config);
        } else {
            $client->useApplicationDefaultCredentials();
        }

        $client->addScope($scopes);

        return $client;
    }
}

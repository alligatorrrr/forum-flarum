<?php


namespace Blomstra\Realtime\Websocket\Api;

use Blomstra\Realtime\Websocket\Settings;

class ForumAttributes
{
    public function __invoke($serializer, $model, $attributes)
    {
        /** @var Settings $settings */
        $settings = resolve(Settings::class);

        return [
            'websocket.key' => $settings->appKey,
            'websocket.host' => $settings->jsClientHost,
            'websocket.port' => $settings->jsClientPort,
            'websocket.secure' => $settings->jsClientSecure,
        ];
    }
}

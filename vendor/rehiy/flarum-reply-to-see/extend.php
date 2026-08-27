<?php

use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Extend;
use Rehiy\ReplyToSee\HideContentInPosts;
use s9e\TextFormatter\Configurator;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/resources/less/forum.less'),

    (new Extend\Formatter)
        ->configure(function (Configurator $config) {
            $config->BBCodes->addCustom(
                '[REPLY]{TEXT}[/REPLY]',
                '<reply2see>{TEXT}</reply2see>'
            );
        }),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\ApiSerializer(BasicPostSerializer::class))
        ->attributes(HideContentInPosts::class),
];

<?php

namespace Nearata\CakeDay;

use Flarum\Extend;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\User\Filter\UserFilterer;
use Nearata\CakeDay\Api\Serializer\BasicUserSerializerAttributes;
use Nearata\CakeDay\Api\Serializer\ForumSerializerAttributes;
use Nearata\CakeDay\Filter\CakedayFilter;
use Nearata\CakeDay\Frontend\AnniversariesRoute;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/anniversaries', 'nearata-cakeday.anniversaries', AnniversariesRoute::class),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(ForumSerializerAttributes::class),

    (new Extend\Filter(UserFilterer::class))
        ->addFilter(CakedayFilter::class),

    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attributes(BasicUserSerializerAttributes::class)
];

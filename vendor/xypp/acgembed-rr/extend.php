<?php
/*
 * This file is part of xypp/acgembed-rr.
 *
 * Copyright (c) 2020 lcinhk.
 * Copyright (c) 2021 ZeQueen.
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
namespace xypp\acgembed;

use Flarum\Api\Controller\ShowDiscussionController;
use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Extend;
use Flarum\Post\Post;


return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    (new Extend\Model(Post::class))
        ->hasMany("media-infos", MediaInfo::class, "post_id"),
    (new Extend\Event())
        ->listen(\Flarum\Post\Event\Saving::class, Listener\PostSaving::class)
        ->listen(\Flarum\Post\Event\Posted::class, Listener\PostPosted::class),
    (new Extend\ApiSerializer(BasicPostSerializer::class))
        ->hasMany('media-infos', Api\Serializer\MediaInfoSerializer::class),
    (new Extend\ApiController(ShowDiscussionController::class))
        ->addInclude('posts.media-infos'),
    (new Extend\Settings())
        ->default('xypp.acgembed.lazy', false)
        ->default('xypp.acgembed.fetch', false)
        ->default('xypp.acgembed.show-url', true)
        ->default('xypp.acgembed-rr.tags', "bilibili,xiami,music163,acfun,niconico,qq,acgtv,youtube"),
    (new Extend\Formatter)
        ->configure(Formatter\Configure::class)
        ->render(Formatter\Render::class),
    new Extend\Locales(__DIR__ . '/locale'),
];
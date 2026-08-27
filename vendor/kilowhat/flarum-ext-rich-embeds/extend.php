<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Api\Controller\CreatePostController;
use Flarum\Api\Controller\ListPostsController;
use Flarum\Api\Controller\ShowDiscussionController;
use Flarum\Api\Controller\UpdatePostController;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Extend;
use Flarum\Post\Event\Saving;
use Flarum\Post\Post;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/resources/less/admin.less'),

    (new Extend\Routes('api'))
        ->post('/kilowhat-rich-embeds/get', 'kilowhat-rich-embeds.get', Controllers\GetEmbedController::class)
        ->get('/kilowhat-rich-embeds/proxy', 'kilowhat-rich-embeds.proxy', Controllers\ImageProxyController::class)
        ->get('/kilowhat-rich-embeds/google-drive-thumbnails/{id}', 'kilowhat-rich-embeds.google-drive-thumbnails', Controllers\GoogleDriveThumbnailController::class),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Formatter)
        ->configure(ConfigureFormatter::class),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(ForumAttributes::class),

    (new Extend\Model(Post::class))
        // The 2 relationships are defined identically, but the link relationship will be populated in the serializer with a subset of the full relationship
        ->belongsToMany('kilowhatLinkRichEmbeds', Embed::class, 'kilowhat_rich_embed_post', 'post_id', 'embed_id')
        ->belongsToMany('kilowhatRichEmbeds', Embed::class, 'kilowhat_rich_embed_post', 'post_id', 'embed_id'),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->hasMany('kilowhatLinkRichEmbeds', EmbedSerializer::class)
        ->hasMany('kilowhatRichEmbeds', EmbedSerializer::class)
        ->attributes(PostAttributes::class),

    (new Extend\ApiController(ListPostsController::class))
        ->addInclude(['kilowhatLinkRichEmbeds', 'kilowhatRichEmbeds'])
        ->load('kilowhatRichEmbeds'),
    (new Extend\ApiController(CreatePostController::class))
        ->addInclude(['kilowhatLinkRichEmbeds', 'kilowhatRichEmbeds']),
    (new Extend\ApiController(UpdatePostController::class))
        ->addInclude(['kilowhatLinkRichEmbeds', 'kilowhatRichEmbeds']),
    (new Extend\ApiController(ShowDiscussionController::class))
        ->addInclude(['posts.kilowhatLinkRichEmbeds', 'posts.kilowhatRichEmbeds'])
        // Only the full relationship is loaded, the other one will be set as a subset of this one in PostAttributes
        ->load('posts.kilowhatRichEmbeds'),

    (new Extend\Event())
        ->listen(Saving::class, Listeners\SavePost::class),

    (new Extend\Console())
        ->command(Commands\ManuallyProcessUrl::class)
        ->command(Commands\RefreshCommand::class)
        ->command(Commands\ScanCommand::class),

    (new Extend\ErrorHandling())
        ->handler(Exceptions\BlacklistedUrl::class, Exceptions\BlacklistedUrlHandler::class)
        ->handler(Exceptions\InvalidUrl::class, Exceptions\InvalidUrlHandler::class),

    (new Extend\Settings())
        ->default('kilowhat-rich-embeds.maxImageCount', 4),
];

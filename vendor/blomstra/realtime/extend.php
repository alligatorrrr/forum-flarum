<?php

namespace Blomstra\Realtime;

use Flarum\Api\Controller\ShowPostController;
use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Extend as Flarum;
use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;

return [
    (new Flarum\ServiceProvider)
        ->register(WebsocketProvider::class),

    (new Flarum\Console)
        ->command(Websocket\Console\HaltCommand::class)
        ->command(Websocket\Console\ServeCommand::class)
        ->command(Websocket\Console\InfoCommand::class),

    (new Flarum\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less')
        ->content(function (Document $document) {
            /** @var SettingsRepositoryInterface $settings */
            $settings = resolve(SettingsRepositoryInterface::class);
            $document->payload['blomstra-realtime.typing-indicator'] = (bool) $settings->get('blomstra-realtime.typing-indicator');
            $document->payload['blomstra-realtime.release-discussion-updates'] = (bool) $settings->get('blomstra-realtime.release-discussion-updates');
        }),

    (new Flarum\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    new Flarum\Locales(__DIR__.'/resources/locale'),

    (new Flarum\Routes('api'))
        ->post('/websocket/auth', 'websocket.auth', Websocket\Api\AuthController::class),

    (new Flarum\ApiSerializer(ForumSerializer::class))
        ->attributes(Websocket\Api\ForumAttributes::class),

    (new Flarum\ApiSerializer(DiscussionSerializer::class))
        ->attribute('canViewWhoTypes', function (DiscussionSerializer $serializer, Discussion $model) {
            $settings = resolve(SettingsRepositoryInterface::class);
            return $serializer->getActor()->can('blomstra-realtime.view-who-types', $model) && (bool) $settings->get('blomstra-realtime.typing-indicator');
        }),

    (new Flarum\ApiSerializer(CurrentUserSerializer::class))
        ->attribute('canViewWhoTypes', function (CurrentUserSerializer $serializer) {
            $settings = resolve(SettingsRepositoryInterface::class);
            return $serializer->getActor()->hasPermissionLike('blomstra-realtime.view-who-types') && (bool) $settings->get('blomstra-realtime.typing-indicator');
        }),

    (new Flarum\Event)
        ->subscribe(Push\Discussion\NewActivity::class)
        ->subscribe(Push\Post\NewActivity::class),

    (new Flarum\Notification)
        ->driver('realtime', Push\NotificationDriver::class),

    // This enables the ability to include discussion.tags
    (new Flarum\ApiController(ShowPostController::class))
        ->addOptionalInclude('discussion.tags'),

    (new Flarum\Settings())
        // In seconds. Defaults to 2 minutes.
        ->default('blomstra-realtime.release-discussion-updates-interval', 120)
        ->default('blomstra-realtime.typing-indicator', true)
        ->default('blomstra-realtime.release-discussion-updates', true)
        ->serializeToForum('blomstra-realtime.release-discussion-updates-interval', 'blomstra-realtime.release-discussion-updates-interval', 'intval'),

    // Disables csrf checks on auth, would time out after being inactive for 60 minutes.
    (new Flarum\Csrf())
        ->exemptRoute('websocket.auth'),

    (new Flarum\User())
        ->registerPreference('blomstra-realtime.typing-indicator-full', 'boolVal', true)
];

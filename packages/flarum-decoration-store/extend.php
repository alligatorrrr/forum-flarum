<?php

use Flarum\Extend;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;
use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;

use Ziven\decorationStore\Console\DecorationStoreScheduleCommand;
use Ziven\decorationStore\Console\PublishSchedule;
use Ziven\decorationStore\Controller\DecorationStoreIndexController;
use Ziven\decorationStore\Controller\ListDecorationStoreController;
use Ziven\decorationStore\Controller\DecorationStoreSortController;
use Ziven\decorationStore\Controller\DecorationStoreUpdateController;
use Ziven\decorationStore\Controller\DecorationStoreAddController;
use Ziven\decorationStore\Controller\DecorationStoreUploadImageController;

use Ziven\decorationStore\Controller\DecorationStorePurchaseController;
use Ziven\decorationStore\Controller\DecorationStorePurchaseUpdateController;
use Ziven\decorationStore\Controller\DecorationStorePurchaseSubscriptionController;
use Ziven\decorationStore\Controller\ListDecorationStorePurchaseHisotryController;
use Ziven\decorationStore\Controller\ListDecorationStoreEquipmentController;
use Ziven\decorationStore\Controller\ListDecorationStoreGalleryController;

use Ziven\decorationStore\Serializer\DecorationStorePurchaseSerializer;

use Ziven\decorationStore\Search\DecorationStoreSearcher;
use Ziven\decorationStore\Search\DecorationStoreGambit;
use Ziven\decorationStore\Search\DecorationStoreFullTextGambit;
use Ziven\decorationStore\Notification\DecorationSubscriptionBlueprint;

$extend = [
    (new Extend\Frontend('admin'))->js(__DIR__.'/js/dist/admin.js')->css(__DIR__.'/less/admin.less'),
    (new Extend\Frontend('forum'))->js(__DIR__ . '/js/dist/forum.js')->css(__DIR__.'/less/forum.less')
        ->route('/decorationStore', 'decorationStore.index', DecorationStoreIndexController::class),

    (new Extend\Locales(__DIR__ . '/locale')),

    (new Extend\Routes('api'))
        ->get('/decorationStoreList', 'decorationStore.get', ListDecorationStoreController::class)
        ->post('/decorationStoreList', 'decorationStore.add', DecorationStoreAddController::class)
        ->post('/decorationStoreList/order', 'decorationStore.order', DecorationStoreSortController::class)
        ->patch('/decorationStoreList/{id}', 'decorationStore.update', DecorationStoreUpdateController::class)
        ->post('/decorationItemImageUpload', 'decorationStore.itemImageUpload', DecorationStoreUploadImageController::class)

        ->post('/decorationStorePurchase', 'decorationStore.purchase', DecorationStorePurchaseController::class)
        ->post('/decorationStorePurchase/subscription', 'decorationStore.subscription', DecorationStorePurchaseSubscriptionController::class)
        ->patch('/decorationStorePurchase/{purchase_id}', 'decorationStore.purchaseUpdate', DecorationStorePurchaseUpdateController::class)
        ->get('/decorationStorePurchaseHistory', 'decorationStore.purchaseHistory', ListDecorationStorePurchaseHisotryController::class)
        ->get('/decorationStoreEquipment', 'decorationStore.equipment', ListDecorationStoreEquipmentController::class)
        ->patch('/decorationStoreEquipment/{purchase_id}', 'decorationStore.equipmentUpdate', DecorationStorePurchaseUpdateController::class)
        ->get('/decorationStoreGallery', 'decorationStore.gallery', ListDecorationStoreGalleryController::class),

    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attribute('decorationAvatarFrame', function (BasicUserSerializer $serializer, User $user) {
            return $user->decoration_avatarFrame;
        })->attribute('decorationProfileBackground', function (BasicUserSerializer $serializer, User $user) {
            return $user->decoration_profileBackground;
        })->attribute('decorationUsernameColor', function (BasicUserSerializer $serializer, User $user) {
            return $user->decoration_usernameColor;
        }),

    (new Extend\Settings())
        ->default('ziven-decoraton-store.decorationStoreItemTypes', ['avatarFrame','profileBackground','usernameColor'])
        ->default('ziven-decoraton-store.decorationStoreTimezone', 'Asia/Shanghai')
        ->serializeToForum('decorationStoreDisplayName', 'ziven-decoraton-store.decorationStoreDisplayName', 'strval')
        ->serializeToForum('decorationStoreTimezone', 'ziven-decoraton-store.decorationStoreTimezone')
        ->serializeToForum('decorationStoreItemTypes', 'ziven-decoraton-store.decorationStoreItemTypes'),

    (new Extend\SimpleFlarumSearch(DecorationStoreSearcher::class))
        ->addGambit(DecorationStoreGambit::class)
        ->setFullTextGambit(DecorationStoreFullTextGambit::class),

    (new Extend\Filesystem())
        ->disk('ziven-decoration-store', function (Paths $paths, UrlGenerator $url) {
            return [
                'root'   => "$paths->public/assets/decorationStore",
                'url'    => $url->to('forum')->path('assets/decorationStore')
            ];
        }),

    (new Extend\Console())
        ->command(DecorationStoreScheduleCommand::class)
        ->schedule(DecorationStoreScheduleCommand::class, new PublishSchedule()),

    (new Extend\Notification())
        ->type(DecorationSubscriptionBlueprint::class, DecorationStorePurchaseSerializer::class, ['alert']),
];

return $extend;
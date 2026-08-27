<?php

namespace Ziven\BadgeShop;

use Flarum\Extend;
use Flarum\Api\Controller as FlarumController;
use Ziven\BadgeShop\Controllers\BadgeShopIndexController;
use Ziven\BadgeShop\Api\Controller\BadgeShopPurchaseController;
use Ziven\BadgeShop\Api\Controller\BadgeShopPurchaseListController;
use Ziven\BadgeShop\Api\Controller\BadgeShopCancelSubscriptionController;
use Ziven\BadgeShop\Api\Serializer\BadgeShopSerializer;
use Ziven\BadgeShop\Notification\BadgeShopBlueprint;
use Flarum\User\User;

return [
    (new Extend\Frontend('admin'))->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/badgeShop', 'badgeShop.index', BadgeShopIndexController::class),

    (new Extend\Locales(__DIR__ . '/locale')),

    (new Extend\Routes('api'))
        ->post('/badgeShopPurchase', 'badgeShop.create', BadgeShopPurchaseController::class)
        ->post('/badgeShopCancelSubscription', 'badgeShop.cancel', BadgeShopCancelSubscriptionController::class)
        ->get('/badgeShopPurchase/{user_id}', 'badgeShop.index', BadgeShopPurchaseListController::class),

    (new Extend\Notification())
        ->type(BadgeShopBlueprint::class, BadgeShopSerializer::class, ['alert']),

    (new Extend\Settings())
        ->serializeToForum('badgeShopDisplayName', 'ziven-badge-shop.badgeShopDisplayName', 'strval')
        ->serializeToForum('badgeShopTest', 'ziven-badge-shop.badgeShopTest', 'strval')
        ->serializeToForum('badgeShopSellBadgeCategory', 'ziven-badge-shop.badgeShopSellBadgeCategory', 'strval')
        ->serializeToForum('badgeShopSellBadgeDetails', 'ziven-badge-shop.badgeShopSellBadgeDetails', function ($raw) {
            $sellBadgeList = json_decode($raw);
            return is_array($sellBadgeList) ? $sellBadgeList : [];
        })
];
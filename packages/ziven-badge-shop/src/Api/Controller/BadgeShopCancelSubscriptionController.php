<?php

namespace Ziven\BadgeShop\Api\Controller;

use Ziven\BadgeShop\Api\Serializer\BadgeShopSerializer;
use Ziven\BadgeShop\Model\BadgeShop;
use V17Development\FlarumUserBadges\UserBadge\UserBadge;
use Ziven\BadgeShop\Notification\BadgeShopBlueprint;

use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Locale\Translator;
use Flarum\Notification\NotificationSyncer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class BadgeShopCancelSubscriptionController extends AbstractCreateController{
    public $serializer = BadgeShopSerializer::class;
    protected $notifications;
    protected $translator;
    protected $settings;

    public function __construct(NotificationSyncer $notifications, Translator $translator,SettingsRepositoryInterface $settings){
        $this->notifications = $notifications;
        $this->translator = $translator;
        $this->settings = $settings;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $requestData = $request->getParsedBody()['data']['attributes'];
        $userId = $requestData['userId'];
        $badgeId = $requestData['badgeId'];
        $purchaseID = $requestData['id'];

        $badgeShopPurchase = BadgeShop::find($purchaseID);

        if($badgeShopPurchase){
            $badgeShopPurchase->delete();
        }
        
        $badge = UserBadge::where(["user_id" => $userId,"badge_id" => $badgeId]);

        if($badge) {
            $badge->delete();
        }

        return $badgeShopPurchase;
    }
}

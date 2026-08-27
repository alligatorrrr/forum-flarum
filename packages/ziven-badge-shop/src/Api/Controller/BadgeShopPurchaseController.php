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
use Flarum\Foundation\ValidationException;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class BadgeShopPurchaseController extends AbstractCreateController{
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
        $moneyName = str_replace("[money]", "", $this->settings->get('antoinefr-money.moneyname'));
        $currentBadgesData = UserBadge::where(["user_id" => $userId])->get();
        $userHaveBadge = false;
        
        for ($i=0;$i<count($currentBadgesData);$i++) {
            if($currentBadgesData[$i]["badge_id"]==$badgeId){
                $userHaveBadge = true;
                break;
            }
        }

        if ($userHaveBadge===true) {
            throw new ValidationException(['message' => $this->translator->trans('ziven-badge-shop.forum.bagde-shop-purchase-failed-already-have')]);
        }

        $userPurchasedBadgeData = BadgeShop::where(['user_id'=>$userId,'badge_id'=>$badgeId])->where('is_expired', 0)->get();
        $userPurchasedBadgeCount = count($userPurchasedBadgeData);

        if ($userPurchasedBadgeCount>0) {
            throw new ValidationException(['message' => $this->translator->trans('ziven-badge-shop.forum.bagde-shop-purchase-failed-already-purchased')]);
        }

        $BadgePurchaseData = $this->getBadgePurchaseData($badgeId);
        $BadgePurchaseType = $BadgePurchaseData->type;
        $BadgePurchaseCost = $BadgePurchaseData->cost;

        $user = User::find($userId);
        $userMoneyRemain = $user->money-$BadgePurchaseCost;

        if($userMoneyRemain<0){
            $needMoneyText = str_replace("[money]", $moneyName, $this->translator->trans('ziven-badge-shop.forum.bagde-shop-purchase-failed-need-money'));

            throw new ValidationException(['message' => $needMoneyText]);
        }

        $badgeShopPurchase = new BadgeShop();
        $badgeShopPurchase->user_id = $userId;
        $badgeShopPurchase->badge_id = $badgeId;
        $badgeShopPurchase->cost = $BadgePurchaseCost;
        $badgeShopPurchase->type = $BadgePurchaseType;
        $badgeShopPurchase->assigned_at = Carbon::now('Asia/Shanghai');
        $badgeShopPurchase->save();

        $badge = UserBadge::build($userId, $badgeId);
        $badge->description = "商店购买";
        $badge->save();

        $user->money = $userMoneyRemain;
        $user->save();

        // Send notification
        $this->notifications->sync(
            new BadgeShopBlueprint($badgeShopPurchase),
            [$badgeShopPurchase->purchasedByUser]
        );

        return $badgeShopPurchase;
    }

    protected function getBadgePurchaseData($badgeId){
        $purchaseData = null;
        $badgeShopSellBadgeDetails = json_decode($this->settings->get('ziven-badge-shop.badgeShopSellBadgeDetails'));

        for ($i=0;$i<count($badgeShopSellBadgeDetails);$i++) {
          if($badgeShopSellBadgeDetails[$i]->id==$badgeId){
            $purchaseData = $badgeShopSellBadgeDetails[$i];
            break;
          }
        }

        return $purchaseData;
    }
}

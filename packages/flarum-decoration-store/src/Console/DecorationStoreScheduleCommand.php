<?php

namespace Ziven\decorationStore\Console;

use Ziven\decorationStore\Model\DecorationStorePurchase;
use Ziven\decorationStore\Model\DecorationStore;
use Ziven\decorationStore\Notification\DecorationSubscriptionBlueprint;
use Ziven\decorationStore\Helpers\CommonHelper;

use Flarum\Console\AbstractCommand;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Flarum\Notification\NotificationSyncer;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Symfony\Contracts\Translation\TranslatorInterface;

class DecorationStoreScheduleCommand extends AbstractCommand{
    protected $bus;
    protected $settings;
    protected $translator;
    protected $notifications;

    public function __construct(NotificationSyncer $notifications, Dispatcher $bus, SettingsRepositoryInterface $settings, TranslatorInterface $translator){
        parent::__construct();
        $this->bus = $bus;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->notifications = $notifications;
    }

    protected function configure(){
        $this->setName('decorationStore:checkDate')->setDescription('Check date');
    }

    protected function fire(){
        // $this->info('Sync starting...');

        DecorationStore::where([["item_discount_days",">",0]])->decrement('item_discount_days');

        $decorationStoreData = DecorationStore::where([["item_discount_days","=",0],["item_discount","!=",0]])->get();

        foreach($decorationStoreData as $key => $value) {
            $value->item_discount = 0;
            $value->save();
        }

        DecorationStorePurchase::where([["expired_days",">",0]])->decrement('expired_days');
        $decorationStorePurchaseData = DecorationStorePurchase::where([["expired_days","=",0],["purchase_type","!=","onetime"],["is_expired","=",0]])->get();
        

        foreach($decorationStorePurchaseData as $key => $value) {
            $itemType = $value->item_type;
            $userID = $value->user_id;
            $itemStatus = $value->item_status;
            $purchaseCount = $value->item_count;
            $purchaseCost = $value->purchase_cost;
            $purchaseDiscount = $value->purchase_discount;
            $purchaseType = $value->purchase_type;
            $purchaseActualCost = (new CommonHelper)->getActualCost($itemData,$purchaseCount);
            $isExpired = false;

            $userData = User::find($userID);
            $userMoney = $userData->money;
            $userMoneyRemain = $userMoney-$purchaseActualCost;

            if($userMoneyRemain<0){
                if($itemType==="avatarFrame"){
                    $userData->decoration_avatarFrame = null;
                }else if($itemType==="profileBackground"){
                    $userData->decoration_profileBackground = null;
                }else if($itemType==="usernameColor"){
                    $userData->decoration_usernameColor = null;
                }

                $isExpired = true;
            }else{
                $userData->money = $userMoneyRemain;

                if($purchaseType==="monthly"){
                    $value->expired_days = 31;
                }else if($purchaseType==="yearly"){
                    $value->expired_days = 365;
                }
            }

            $userData->save();

            if($isExpired===true){
                $value->item_status = 0;
                $value->is_expired = 1;
            }

            $value->save();
            $this->notifications->sync(new DecorationSubscriptionBlueprint($value),[$userData]);
        }

        // $this->info('Sync done.');
    }
    
}

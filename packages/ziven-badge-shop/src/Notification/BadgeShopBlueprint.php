<?php

namespace Ziven\BadgeShop\Notification;

use Ziven\BadgeShop\Model\BadgeShop;
use Flarum\Notification\Blueprint\BlueprintInterface;

class BadgeShopBlueprint implements BlueprintInterface{
    public $badgePurchase;

    public function __construct(BadgeShop $badge){
        $this->badgePurchase = $badge;
    }

    public function getSubject(){
        return $this->badgePurchase;
    }

    public function getFromUser(){
        return $this->badgePurchase->purchasedByUser;
    }

    public function getData(){
        return null;
    }
    
    public static function getType(){
        return 'badgeShop';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel(){
        return BadgeShop::class;
    }
}

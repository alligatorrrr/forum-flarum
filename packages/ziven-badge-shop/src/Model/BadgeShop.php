<?php

namespace Ziven\BadgeShop\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Formatter\Formatter;
use Flarum\User\User;
use V17Development\FlarumUserBadges\Badge\Badge;

class BadgeShop extends AbstractModel
{
    use ScopeVisibilityTrait;

    protected $table = 'ziven_badge_shop_user';
    protected static $formatter;

    public function purchasedByUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function badge(){
        return $this->belongsTo(Badge::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function getFormatter(){
        return static::$formatter;
    }

    public static function setFormatter(Formatter $formatter){
        static::$formatter = $formatter;
    }
}

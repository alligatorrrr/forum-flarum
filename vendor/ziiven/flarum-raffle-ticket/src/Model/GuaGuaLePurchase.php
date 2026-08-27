<?php

namespace Ziven\GuaGuaLe\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Ziven\GuaGuaLe\Model\GuaGuaLe;
use Flarum\User\User;

class GuaGuaLePurchase extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_guaguale_purchase';

    public function guagualeData(){
        return $this->hasOne(GuaGuaLe::class, 'id', 'gua_id');
    }

    public function purchasedUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

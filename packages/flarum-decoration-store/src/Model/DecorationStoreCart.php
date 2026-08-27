<?php

namespace Ziven\decorationStore\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Ziven\decorationStore\Model\DecorationStore;
use Flarum\User\User;

class DecorationStoreCart extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_decoration_store_cart';

    public function decorationData(){
        return $this->hasOne(DecorationStore::class, 'id', 'item_id');
    }

    public function fromUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

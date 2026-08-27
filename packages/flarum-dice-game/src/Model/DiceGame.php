<?php

namespace Ziven\DiceGame\Model;

use Ziven\DiceGame\Model\DiceGameUser;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\User\User;

class DiceGame extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_dice_game';

    public function hostUserData(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function participateUsers(){
        return $this->belongsToMany(User::class,DiceGameUser::class,'game_id','user_id');
    }
}

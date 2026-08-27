<?php

namespace Ziven\DiceGame\Model;

use Ziven\DiceGame\Model\DiceGame;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\User\User;

class DiceGameUser extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_dice_game_user';

    public function gameUserData(){
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    public function diceGameData(){
        return $this->hasOne(DiceGame::class, 'id', 'game_id');
    }

    public function userData(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

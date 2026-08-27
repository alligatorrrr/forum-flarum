<?php
namespace Ziven\DiceGame\Serializer;

use Ziven\DiceGame\Serializer\DiceGameSerializer;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

class DiceGameUserSerializer extends AbstractSerializer{
    protected $type = 'zivenDiceGameUser';

    protected function getDefaultAttributes($data){
        return [
            'id' => $data->id,
            'dice' => $data->dice,
            'user_id' => $data->user_id,
            'wager' => $data->wager,
            'result' => $data->result,
            'assignedAt' => $data->assigned_at,
        ];
    }

    protected function gameUserData($data){
        return $this->hasOne($data, BasicUserSerializer::class);
    }

    protected function diceGameData($data){
        return $this->hasOne($data, DiceGameSerializer::class);
    }

    protected function userData($data){
        return $this->hasOne($data, BasicUserSerializer::class);
    }
}

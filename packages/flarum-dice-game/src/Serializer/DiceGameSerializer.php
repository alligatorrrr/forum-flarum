<?php
namespace Ziven\DiceGame\Serializer;

use Ziven\DiceGame\Serializer\DiceGameUserSerializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

class DiceGameSerializer extends AbstractSerializer{
    protected $type = 'zivenDiceGame';

    protected function getDefaultAttributes($data){
        return [
            'id' => $data->id,
            'dice' => $data->dice,
            'defeatCount' => $data->defeat_count,
            'challengeCount' => $data->challenge_count,
            'wager' => $data->wager,
            'balance' => $data->balance,
            'gamePlayedID' => $data->gamePlayedID,
            'assignedAt' => $data->assigned_at,
        ];
    }

    protected function hostUserData($data){
        return $this->hasOne($data, BasicUserSerializer::class);
    }

    protected function participateUsers($data){
        return $this->hasMany($data, BasicUserSerializer::class);
    }
}

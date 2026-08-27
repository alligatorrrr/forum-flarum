<?php
namespace Ziven\DiceGame\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

class DiceGameSummarySerializer extends AbstractSerializer{
    protected $type = 'zivenDiceGameSummary';

    protected function getDefaultAttributes($data){
        return [
            'id' => $data["id"],
            'winCount' => $data["winCount"],
            'defeatCount' => $data["defeatCount"],
        ];
    }
}

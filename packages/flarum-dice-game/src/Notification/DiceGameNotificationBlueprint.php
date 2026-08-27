<?php

namespace Ziven\DiceGame\Notification;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Illuminate\Support\Carbon;

use Ziven\DiceGame\Model\DiceGame;

class DiceGameNotificationBlueprint implements BlueprintInterface{
    public $data;

    public function __construct(DiceGame $data){
        $this->data = $data;
    }

    public function getSubject(){
        return $this->data;
    }

    public function getFromUser(){
        return null;
    }

    public function getData(){
        return [];
    }
    
    public static function getType(){
        return 'zivenDiceGame';
    }

    public static function getSubjectModel(){
        return DiceGame::class;
    }
}

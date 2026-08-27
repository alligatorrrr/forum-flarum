<?php

namespace Ziven\DiceGame\Controller;

use Ziven\DiceGame\Serializer\DiceGameSerializer;
use Ziven\DiceGame\Model\DiceGame;

use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CreateDiceGameController extends AbstractCreateController{
    public $serializer = DiceGameSerializer::class;
    protected $settings;
    protected $translator;

    public function __construct(Translator $translator,SettingsRepositoryInterface $settings){
        $this->settings = $settings;
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $allowDiceGame = $actor->can('ziven.zivenAllowDiceGame');

        if($allowDiceGame===false){
            return [];
        }

        $currentUserID = $actor->id;
        $requestData = $request->getParsedBody()['data']['attributes'];
        $errorMessage = "";

        if(!isset($requestData)){
            $errorMessage = 'ziven-dice-game.forum.save-error';
            goto error;
        }

        $wager = intval($requestData['wager']);
        $quantity = intval($requestData['quantity']);
        $minChallengeWager = $this->settings->get('ziven-dice-game.minChallengeWager');
        $maxChallengeCount = $this->settings->get('ziven-dice-game.maxChallengeCount');

        if(!is_numeric($wager) || !is_numeric($quantity) || $wager<$minChallengeWager || $quantity>$maxChallengeCount){
            $errorMessage = 'ziven-dice-game.forum.save-error';
            goto error;
        }

        $currentUserData = User::find($currentUserID);
        $currentUserMoneyRemain = $currentUserData->money-$wager;

        if($currentUserMoneyRemain<0){
            $errorMessage = 'ziven-dice-game.forum.save-error-insufficient-fund';
            goto error;
        }

        $currentUserData->money = $currentUserMoneyRemain;
        $currentUserData->save();
        
        $diceGameData = new DiceGame();
        $diceGameData->user_id = $currentUserID;
        $diceGameData->dice = mt_rand(1,6);
        $diceGameData->challenge_count = $quantity;
        $diceGameData->wager = $wager;
        $diceGameData->balance = $wager;
        $diceGameData->assigned_at = Carbon::now("Asia/ShangHai");
        $diceGameData->save();

        return $diceGameData;

        error:
        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

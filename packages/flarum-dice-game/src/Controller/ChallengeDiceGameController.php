<?php

namespace Ziven\DiceGame\Controller;

use Ziven\DiceGame\Serializer\DiceGameUserSerializer;
use Ziven\DiceGame\Model\DiceGame;
use Ziven\DiceGame\Model\DiceGameUser;
use Ziven\DiceGame\Notification\DiceGameNotificationBlueprint;

use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Notification\NotificationSyncer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ChallengeDiceGameController extends AbstractCreateController{
    public $serializer = DiceGameUserSerializer::class;
    public $include = ["diceGameData"];
    protected $settings;
    protected $translator;
    protected $notifications;

    public function __construct(Translator $translator,SettingsRepositoryInterface $settings,NotificationSyncer $notifications){
        $this->settings = $settings;
        $this->translator = $translator;
        $this->notifications = $notifications;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $allowDiceGame = $actor->can('ziven.zivenAllowDiceGame');
        $include = $this->extractInclude($request);

        if($allowDiceGame===false){
            return [];
        }

        $currentUserID = $actor->id;
        $requestData = $request->getParsedBody()['data']['attributes'];
        $gameID = $requestData["gameID"];
        $errorMessage = "";

        $gameData = DiceGame::select('ziven_dice_game.*','ziven_dice_game_user.id as gamePlayedID')
        ->leftJoin('ziven_dice_game_user', function($join) use ($currentUserID) {
            $join->on('ziven_dice_game.id', '=', 'ziven_dice_game_user.game_id');
            $join->where('ziven_dice_game_user.user_id', '=', $currentUserID);
        })->where([["ziven_dice_game.id","=",$gameID]])->first();

        $gameHostUserID = $gameData->user_id;
        $gameHostDice = $gameData->dice;
        $gameHostBalance = $gameData->balance;
        $gameWager = $gameData->wager;
        $gamePlayedID = $gameData->gamePlayedID;
        $challengeCount = $gameData->challenge_count;

        if(isset($gamePlayedID)){
            $errorMessage = 'ziven-dice-game.forum.game-played';
            goto error;
        }

        if(!isset($gameData) || $gameHostUserID===$currentUserID){
            $errorMessage = 'ziven-dice-game.forum.save-error';
            goto error;
        }

        if($gameHostBalance<=0){
            $errorMessage = 'ziven-dice-game.forum.game-ended';
            goto error;
        }

        $currentUserData = User::find($currentUserID);
        $currentUserMoneyRemain = $currentUserData->money-$gameWager;

        if($currentUserMoneyRemain<0){
            $errorMessage = 'ziven-dice-game.forum.save-error-insufficient-fund';
            goto error;
        }

        $gameResult = 2;
        $userChallengeDice = mt_rand(1,6);

        if($userChallengeDice<$gameHostDice){
            $gameResult = 0;
            $currentUserData->money = $currentUserMoneyRemain;
            $gameData->balance+=$gameWager;
            $gameData->win_count+=1;
        }else if($userChallengeDice>$gameHostDice){
            $gameResult = 1;
            $currentUserData->money+=$gameWager;
            $gameData->balance-=$gameWager;
            $gameData->defeat_count+=1;
        }else{
            $gameData->draw_count+=1;
        }

        $gameEnd = false;
        $reachChallengeLimit = $gameData->defeat_count+$gameData->win_count+$gameData->draw_count>=$challengeCount;

        if($reachChallengeLimit || $gameData->balance<=0){
            $gameData->status = 1;
            $gameEnd = true;
        }

        $gameData->save();
        $currentUserData->save();
        
        $diceGameUserData = new DiceGameUser();
        $diceGameUserData->game_id = $gameID;
        $diceGameUserData->user_id = $currentUserID;
        $diceGameUserData->dice = $userChallengeDice;
        $diceGameUserData->wager = $gameWager;
        $diceGameUserData->result = $gameResult;
        $diceGameUserData->assigned_at = Carbon::now("Asia/ShangHai");
        $diceGameUserData->save();

        $this->loadRelations(new Collection([$diceGameUserData]), $include);

        if($gameEnd){
            $hostUserData = User::find($gameHostUserID);
            $hostUserData->money+=$gameData->balance;
            $hostUserData->save();

            $this->notifications->sync(new DiceGameNotificationBlueprint($gameData),[$hostUserData]);
        }

        return $diceGameUserData;

        error:
        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

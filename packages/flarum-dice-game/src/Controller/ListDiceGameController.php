<?php

namespace Ziven\DiceGame\Controller;

use Ziven\DiceGame\Serializer\DiceGameSerializer;
use Ziven\DiceGame\Model\DiceGame;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;

class ListDiceGameController extends AbstractListController{
    public $serializer = DiceGameSerializer::class;
    public $include = ["hostUserData"];
    protected $url;

    public function __construct(UrlGenerator $url){
        $this->url = $url;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $allowDiceGame = $actor->can('ziven.zivenAllowDiceGame');

        if($allowDiceGame===false){
            return [];
        }

        $currentUserID = $actor->id;
        $include = $this->extractInclude($request);
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $params = $request->getQueryParams();
        $filter = $params["filter"];

        $condition1 = ["ziven_dice_game_user.id"];
        $condition2 = [["ziven_dice_game.status","=",0]];

        if($filter==="recentGame"){
            $condition1 = [];
            $condition2 = [["ziven_dice_game.status","=",1],["ziven_dice_game.user_id","=",$currentUserID]];
            array_push($this->include,"participateUsers.userData");
        }

        $diceGameData = DiceGame::select('ziven_dice_game.*','ziven_dice_game_user.id as gamePlayedID')
        ->leftJoin('ziven_dice_game_user', function($join) use ($currentUserID,$filter) {
            $join->on('ziven_dice_game.id', '=', 'ziven_dice_game_user.game_id');
            $join->where('ziven_dice_game_user.user_id', '=', $currentUserID);
        })
        ->whereNull($condition1)
        ->where($condition2)
        ->orderBy("id","desc")
        ->skip($offset)
        ->take($limit + 1)
        ->get();

        $this->loadRelations($diceGameData, $include);

        $hasMoreResults = $limit > 0 && $diceGameData->count() > $limit;

        if($hasMoreResults){
            $diceGameData->pop();
        }

        $document->addPaginationLinks(
            $this->url->to('api')->route('zivenDiceGame.get'),
            $params,
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );

        foreach ($diceGameData as $value) {
            if($value->user_id!==$currentUserID){
                $value->dice = 0;
                $value->balance = 0;
            }
        }

        return $diceGameData;
    }
}

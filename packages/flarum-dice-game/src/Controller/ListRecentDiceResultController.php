<?php

namespace Ziven\DiceGame\Controller;

use Ziven\DiceGame\Serializer\DiceGameUserSerializer;
use Ziven\DiceGame\Model\DiceGameUser;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;

class ListRecentDiceResultController extends AbstractListController{
    public $serializer = DiceGameUserSerializer::class;
    public $include = ["diceGameData","gameUserData"];
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
        $filters = $this->extractFilter($request);

        $condition = [["ziven_dice_game_user.user_id","=",$currentUserID]];

        $diceGameData = DiceGameUser::select('ziven_dice_game.user_id as creator_id','ziven_dice_game_user.*')
        ->leftJoin('ziven_dice_game', function($join) {
            $join->on('ziven_dice_game_user.game_id', '=', 'ziven_dice_game.id');
        })
        ->where($condition)
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
            $this->url->to('api')->route('zivenDiceGameResult.get'),
            $params,
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );

        return $diceGameData;
    }
}

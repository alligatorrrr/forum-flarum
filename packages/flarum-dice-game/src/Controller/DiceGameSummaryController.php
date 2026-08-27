<?php

namespace Ziven\DiceGame\Controller;

use Ziven\DiceGame\Serializer\DiceGameSummarySerializer;
use Ziven\DiceGame\Model\DiceGameUser;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class DiceGameSummaryController extends AbstractListController{
    public $serializer = DiceGameSummarySerializer::class;
    public $include = [];
    protected $settings;
    protected $translator;

    public function __construct(Translator $translator,SettingsRepositoryInterface $settings){
        $this->settings = $settings;
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $include = $this->extractInclude($request);
        $actor = $request->getAttribute('actor');
        $allowDiceGame = $actor->can('ziven.zivenAllowDiceGame');

        if($allowDiceGame===false){
            return [];
        }

        $currentUserID = $actor->id;
        $winCount = DiceGameUser::where([["user_id","=",$currentUserID],["result","=",1]])->count();
        $defeatCount = DiceGameUser::where([["user_id","=",$currentUserID],["result","=",0]])->count();

        $output = [
            "id"=>1,
            "winCount"=>$winCount,
            "defeatCount"=>$defeatCount,
        ];

        return [$output];
    }
}

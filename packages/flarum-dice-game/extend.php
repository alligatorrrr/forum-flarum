<?php

use Flarum\Extend;
use Flarum\Api\Serializer\ForumSerializer;

use Ziven\DiceGame\Controller\DiceGameController;
use Ziven\DiceGame\Controller\DiceGameSummaryController;
use Ziven\DiceGame\Controller\CreateDiceGameController;
use Ziven\DiceGame\Controller\ChallengeDiceGameController;
use Ziven\DiceGame\Controller\ListDiceGameController;
use Ziven\DiceGame\Controller\ListRecentDiceResultController;
use Ziven\DiceGame\Notification\DiceGameNotificationBlueprint;
use Ziven\DiceGame\Serializer\DiceGameSerializer;

$extend = [
    (new Extend\Frontend('admin'))->js(__DIR__.'/js/dist/admin.js')->css(__DIR__.'/less/admin.less'),
    (new Extend\Frontend('forum'))->js(__DIR__ . '/js/dist/forum.js')->css(__DIR__.'/less/forum.less')
    ->route('/zivenDiceGame', 'zivenDiceGame.index', DiceGameController::class),

    (new Extend\Locales(__DIR__ . '/locale')),

    (new Extend\Routes('api'))
        ->get('/zivenDiceGame', 'zivenDiceGame.get', ListDiceGameController::class)
        ->get('/zivenDiceGameResult', 'zivenDiceGameResult.get', ListRecentDiceResultController::class)
        ->get('/zivenDiceGameSummary', 'zivenDiceGameSummary.get', DiceGameSummaryController::class)
        ->post('/zivenDiceGame', 'zivenDiceGame.create', CreateDiceGameController::class)
        ->post('/zivenDiceGameUser', 'zivenDiceGameUser.create', ChallengeDiceGameController::class),

    (new Extend\Settings())
        ->default('ziven-dice-game.maxChallengeCount', 10)
        ->default('ziven-dice-game.minChallengeWager', 10)
        ->serializeToForum('maxChallengeCount', 'ziven-dice-game.maxChallengeCount', 'intval')
        ->serializeToForum('minChallengeWager', 'ziven-dice-game.minChallengeWager', 'intval'),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('zivenAllowDiceGame', function (ForumSerializer $serializer) {
            return $serializer->getActor()->hasPermission("ziven.zivenAllowDiceGame");
        }),

    (new Extend\Notification())
        ->type(DiceGameNotificationBlueprint::class, DiceGameSerializer::class, ['alert']),
];

return $extend;
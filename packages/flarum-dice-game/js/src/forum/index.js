import app from 'flarum/forum/app';
import { extend } from 'flarum/extend';
import IndexPage from 'flarum/components/IndexPage';
import LinkButton from 'flarum/components/LinkButton';
import NotificationGrid from "flarum/components/NotificationGrid";

import DiceGame from './model/DiceGame';
import DiceGameUser from './model/DiceGameUser';
import DiceGameSummary from './model/DiceGameSummary';
import DiceGameIndexPage from './components/DiceGameIndexPage';
import DiceGameNotification from './components/DiceGameNotification';

app.initializers.add('ziven-dice-game', () => {
  app.store.models.zivenDiceGame = DiceGame;
  app.store.models.zivenDiceGameUser = DiceGameUser;
  app.store.models.zivenDiceGameSummary = DiceGameSummary;
  app.notificationComponents.zivenDiceGame = DiceGameNotification;

  app.routes['zivenDiceGame'] = {
    path: '/zivenDiceGame',
    component: DiceGameIndexPage,
  };

  extend(NotificationGrid.prototype, "notificationTypes", function (items) {
    items.add("zivenDiceGame", {
        name: "zivenDiceGame",
        icon: "fas fa-dice",
        label: app.translator.trans(
            "ziven-dice-game.forum.notification-ziven-dice-game"
        ),
    });
});

  extend(IndexPage.prototype, 'navItems', function (items) {
    const allowDiceGame = app.forum.attribute('zivenAllowDiceGame');

    if(allowDiceGame){
      items.add(
        'ZivenDiceGame',
        <LinkButton icon="fas fa-dice" href={app.route('zivenDiceGame')}>
          {app.translator.trans('ziven-dice-game.forum.dice-game')}
        </LinkButton>,
        15
      );
    }

    return items;
  });
  
  console.group('%c Ziven Dice Game %c Initialized ', 'background-color: #fbbc04; color: #ffffff; font-weight: bold;', 'background-color: green; color: #ffffff; font-weight: bold;');
  console.groupEnd();
});
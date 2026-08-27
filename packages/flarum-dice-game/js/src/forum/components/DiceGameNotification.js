import app from 'flarum/forum/app';
import Notification from "flarum/components/Notification";

export default class DiceGameNotification extends Notification {
  icon() {
    return "fas fa-dice";
  }

  href() {
    return app.route('zivenDiceGame',{t:"recentGame"});
  }

  content() {
    const notification = this.attrs.notification.subject();

    return app.translator.trans('ziven-dice-game.forum.notification-game-content');
  }

  excerpt() {
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const notification = this.attrs.notification.subject();
    const challengeCount = notification.challengeCount();
    const defeatCount = notification.defeatCount();
    const gameID = notification.id();
    const balance = notification.balance();
    let balanceText = moneyName.replace('[money]', balance);

    if(balance>0){
      balanceText = "+"+balanceText;
    }

    return app.translator.trans('ziven-dice-game.forum.notification-game-excerpt',{
      gameID:gameID,
      challengeCount:challengeCount,
      defeatCount:defeatCount,
      balance:balanceText,
    });
  }
}

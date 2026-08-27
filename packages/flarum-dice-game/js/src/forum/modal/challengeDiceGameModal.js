import Modal from 'flarum/common/components/Modal';
import Stream from 'flarum/utils/Stream';
import Button from 'flarum/components/Button';

import challengeDiceGameResultModal from "./challengeDiceGameResultModal";

export default class challengeDiceGameModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    this.gameData = this.attrs.gameData;
    this.loading = false;
    this.minChallengeWager = app.forum.attribute("minChallengeWager");
    this.maxChallengeCount = app.forum.attribute("maxChallengeCount");
    this.currentUser = app.session.user;
  }

  className() {
    return 'Modal--small';
  }

  title() {
    return app.translator.trans('ziven-dice-game.forum.challenge-game');
  }

  content() {
    const userName = this.currentUser.displayName();
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const userMoney = this.currentUser.attribute("money");
    const userMoneyText = moneyName.replace('[money]', userMoney);
    const gameWager = this.gameData.wager();
    const gameWagerText = moneyName.replace('[money]', gameWager);
    const hasMoney = userMoney>=gameWager;
    
    return [
      <div className="Modal-body" style="text-align: center;">
        <div className="Form-group ZivenDiceGameUserName">
          {userName}
        </div>
        <div className="Form-group">
          {app.translator.trans('ziven-dice-game.forum.challenge-game-info',{money:userMoneyText,wager:gameWagerText})}
        </div>

        {hasMoney===true && (
          <div style="display: flex;justify-content: center;column-gap: 10px;">
            <Button disabled={this.loading} className="Button Button--primary" style="min-width:60px" onclick={(e) => {
              this.confirm(e);
            }} >
              {app.translator.trans('ziven-dice-game.forum.confirm')}
            </Button>
            <Button disabled={this.loading} className="Button ZiveDiceGameButton--gray" style="min-width:60px" onclick={() => {
              this.hide();
            }}>
              {app.translator.trans('ziven-dice-game.forum.cancel')}
            </Button>
          </div>
        )}

        {hasMoney===false && (
          <div>
            {app.translator.trans('ziven-dice-game.forum.challenge-game-info-insufficient-fund')}
          </div>
        )}
      </div>
    ];
  }

  confirm(e){
    e.preventDefault();

    this.loading = true;
    const gameID = this.gameData.id();

    const submitData = {
      gameID:gameID,
    };

    app.store
    .createRecord("zivenDiceGameUser")
    .save(submitData)
    .then((result) => {
        const gamePlayedID = result.id();
        const challengeWager = result.wager();
        app.store.getById("zivenDiceGame",gameID).data.attributes.gamePlayedID = gamePlayedID;

        const challengeResult = result.result();
        let wager = 0;

        if(challengeResult===0){
          wager = -challengeWager;
        }else if(challengeResult===1){
          wager = challengeWager;
        }

        app.store.getById("users",this.currentUser.id()).data.attributes.money+=wager;
        app.modal.show(challengeDiceGameResultModal,{gameResult:result,gameID:gameID});
      }
    )
    .catch((e) => {
      this.loading = false;
    });
  }
}

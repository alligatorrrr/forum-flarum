import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/components/Button';
import diceIcon from "../helper/dice";

export default class challengeDiceGameResultModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    this.gameResult = this.attrs.gameResult;
  }

  className() {
    return 'Modal--small';
  }

  title() {
    return app.translator.trans('ziven-dice-game.forum.challenge-game-result');
  }

  content() {
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const gameResult = this.gameResult;
    const diceGameData = gameResult.diceGameData();
    const challengeDice = gameResult.dice();
    const hostDice = diceGameData.dice();
    const result = gameResult.result();
    const wager = gameResult.wager();
    const wagerText = moneyName.replace('[money]', wager);
    let resultText = "draw";
    let resultTextClassName = "ZivenDiceGameDraw";

    if(result===0){
      resultText = "lose";
      resultTextClassName = "ZivenDiceGameLose";
    }else if(result===1){
      resultText = "win";
      resultTextClassName = "ZivenDiceGameWin";
    }

    return [
      <div className="Modal-body" style="text-align: center;">
        <div className={"ZivenDiceGameResult "+resultTextClassName}>
          {app.translator.trans('ziven-dice-game.forum.challenge-game-result-'+resultText)}
        </div>
        <div className="ZivenDiceGameResultDice ZivenDiceGameResultModalDice">
            <span>{diceIcon(challengeDice,40,"darkblue")}</span>
            <span className="ZivenDiceGameResultModalDiceVs">vs</span>
            <span>{diceIcon(hostDice,40,"darkred")}</span>
          </div>
        <div className="Form-group ZivenDiceGameResultInfo">
          {app.translator.trans('ziven-dice-game.forum.challenge-game-result-'+resultText+'-info',{money:wagerText})}
        </div>
        <div style="display: flex;justify-content: center;column-gap: 10px;">
          <Button className="Button ZiveDiceGameButton--gray" style="min-width:60px" onclick={() => {
            this.hide();
          }}>
            {app.translator.trans('ziven-dice-game.forum.close')}
          </Button>
        </div>
      </div>
    ];
  }
}

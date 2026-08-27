import Component from "flarum/Component";
import diceIcon from "../helper/dice";

export default class DiceGameResultListItem extends Component {
  view() {
    const {diceGameListItem} = this.attrs;
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const diceGameData = diceGameListItem.diceGameData();
    const hostUserData = diceGameListItem.gameUserData();
    const assignedAt = diceGameListItem.assignedAt();
    const gameWager = diceGameData.wager();
    let wagerText = moneyName.replace('[money]', gameWager);
    const diceGameID = diceGameData.id();
    const hostDice = diceGameData.dice();
    const challengeDice = diceGameListItem.dice();
    const gameResult = diceGameListItem.result();
    let gameResultText = "challenge-game-result-draw";
    let gameResultClassName = "ZivenDiceGameDraw"

    if(gameResult===0){
      gameResultText = "challenge-game-result-lose";
      gameResultClassName = "ZivenDiceGameLose";
      wagerText = "-"+wagerText;
    }else if(gameResult===1){
      gameResultText = "challenge-game-result-win";
      gameResultClassName = "ZivenDiceGameWin";
      wagerText = "+"+wagerText;
    }

    return (
      <div className="ZivenDiceGameListItemContainer">
        <div className="ZivenDiceGameItemContainer ZivenDiceGameResultItemContainer">
          <div className={"ZivenDiceGameResultContainer "+gameResultClassName}>
            <div className="ZivenDiceGameResultText">{app.translator.trans('ziven-dice-game.forum.'+gameResultText)}</div>
            <div className="ZivenDiceGameResultDice">
              <span>{diceIcon(challengeDice,24,"darkblue")}</span>
              <span style="color:var(--text-color)">vs</span>
              <span>{diceIcon(hostDice,24,"darkred")}</span>
            </div>
          </div>
          <div>
            <div className="ZivenDiceGameListItem">
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-game-id')}</span>
                {diceGameID}
              </div>
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-settlement')}</span>
                <label className={gameResultClassName}>{wagerText}</label>
              </div>
            </div>
            <div className="ZivenDiceGameListItem ZivenDiceGameListItemDesktop">
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-host')}</span>
                {hostUserData.displayName()}
              </div>
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-challenge-createAt')}</span>
                {assignedAt}
              </div>
            </div>
          </div>
        </div>

        <div className="ZivenDiceGameListItemMobile">
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-host')}</span>
                {hostUserData.displayName()}
              </div>
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.item-challenge-createAt')}</span>
            {assignedAt}
          </div>
        </div>
      </div>
    );
  }
}

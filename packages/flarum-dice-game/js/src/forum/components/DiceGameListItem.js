import Component from "flarum/Component";
import Button from 'flarum/components/Button';

import challengeDiceGameModal from "../modal/challengeDiceGameModal";
import diceIcon from "../helper/dice";

export default class DiceGameListItem extends Component {
  view() {
    const {diceGameListItem,category} = this.attrs;
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const currentUserID = app.session.user.id();
    const diceGameID = diceGameListItem.id();
    const hostUserData = diceGameListItem.hostUserData();
    const participateUsers = diceGameListItem.participateUsers();
    const hostUserID = hostUserData.id();
    const gamePlayedID = diceGameListItem.gamePlayedID();
    const gameWager = diceGameListItem.wager();
    const gameBalance = diceGameListItem.balance();
    const assignedAt = diceGameListItem.assignedAt();
    const dice = diceGameListItem.dice();
    const defeatCount = diceGameListItem.defeatCount();
    let wagerText = ""
    let wagerTextClassName = "";
    let challengedUserList;

    if(category=="index"){
      wagerText = moneyName.replace('[money]', gameWager);
    }else{
      wagerText = moneyName.replace('[money]', gameBalance);

      if(gameBalance>0){
        wagerText = "+"+wagerText;
        wagerTextClassName = "ZivenDiceGameWin";
      }

      if(participateUsers){
        challengedUserList = (
          <div>
            {participateUsers.map((participateUserData) => {
              const userName = participateUserData.displayName();
    
              return (
                <span>
                  {userName}
                </span>
              );
            })}
          </div>
        );
      }
    }

    return (
      <div className="ZivenDiceGameListItemContainer">
        <div className="ZivenDiceGameItemContainer">
          <div className="ZivenDiceGameActionContainer">
              {currentUserID===hostUserID && (
                <span>{diceIcon(dice)}</span>
              )}
              {(currentUserID!==hostUserID && gamePlayedID===null) && (
                <Button className={'Button Button--primary'} onclick={() => app.modal.show(challengeDiceGameModal,{gameData:diceGameListItem})}>
                  {app.translator.trans('ziven-dice-game.forum.challenge-game')}
                </Button>
              )}
          </div>
          <div>
            <div className="ZivenDiceGameListItem">
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-id')}</span>
                {diceGameID}
              </div>
              <div>
                <span>
                  {category==="index"?app.translator.trans('ziven-dice-game.forum.item-wager'):app.translator.trans('ziven-dice-game.forum.item-settlement')}
                </span>
                <label className={wagerTextClassName}>{wagerText}</label>
              </div>
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-defeat')}</span>
                {defeatCount}
              </div>
            </div>
            <div className="ZivenDiceGameListItem ZivenDiceGameListItemDesktop">
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-host')}</span>
                {hostUserData.displayName()}
              </div>
              <div>
                <span>{app.translator.trans('ziven-dice-game.forum.item-createAt')}</span>
                {assignedAt}
              </div>
            </div>
            {category==="recentGame" && (
              <details className="ZivenDiceGameParticipateUserDetails ZivenDiceGameListItemDesktop">
                <summary>{app.translator.trans('ziven-dice-game.forum.item-challenged-users')}</summary>
                {challengedUserList}
              </details>
            )}
          </div>
        </div>

        <div className="ZivenDiceGameListItemMobile">
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.item-host')}</span>
            {hostUserData.displayName()}
          </div>
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.item-createAt')}</span>
            {assignedAt}
          </div>
          {category==="recentGame" && (
            <details className="ZivenDiceGameParticipateUserDetails">
              <summary>{app.translator.trans('ziven-dice-game.forum.item-challenged-users')}</summary>
              {challengedUserList}
            </details>
          )}
        </div>
      </div>
    );
  }
}

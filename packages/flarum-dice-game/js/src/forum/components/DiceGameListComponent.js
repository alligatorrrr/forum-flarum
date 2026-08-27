import Component from "flarum/Component";
import Button from 'flarum/components/Button';
import LoadingIndicator from "flarum/components/LoadingIndicator";
import avatar from "flarum/helpers/avatar";
import username from "flarum/helpers/username";

import DiceGameListItem from "./DiceGameListItem";
import startDiceGameModal from "../modal/startDiceGameModal";

export default class DiceGameListComponent extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.titleText = app.translator.trans("ziven-dice-game.forum.dice-game");
    this.category = this.attrs.category;
    this.loading = true;
    this.moreResults = false;
    this.diceGameList = [];
    this.winCount = "-";
    this.defeatCount = "-";
    this.loadResults();
    this.loadSummary();

    const _this = this;

    $(window).off("zivenDiceGameRefresh").on("zivenDiceGameRefresh",function(e){
      _this.refresh();
    });
  }

  view() {
    let loading;
    const allowDiceGame = app.forum.attribute("zivenAllowDiceGame");

    if(allowDiceGame===false){
      return [];
    }

    if(this.loading){
      loading = LoadingIndicator.component({size: "large"});
    }
    
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const userMoneyText = moneyName.replace('[money]', app.session.user.attribute("money"));

    return (
      <div>
        <div className="ZivenDiceGameHeader">
          <Button className={'Button Button--primary'} disabled={this.loading} onclick={() => app.modal.show(startDiceGameModal)}>
            {app.translator.trans('ziven-dice-game.forum.start-game')}
          </Button>
          <Button style="margin-left: 10px;" className={'Button'} disabled={this.loading} onclick={() => m.route.set(app.route('zivenDiceGame',{t:"recentGame"}))}>
            {app.translator.trans('ziven-dice-game.forum.recent-result')}
          </Button>
        </div>

        <div className="ZivenDiceGameSummary">
          <div className="ZivenDiceGameSummaryUser">
            {avatar(app.session.user)}{username(app.session.user)}
          </div>
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.money-count')}</span>
            {userMoneyText}
          </div>
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.win-count')}</span>
            {this.winCount}
          </div>
          <div>
            <span>{app.translator.trans('ziven-dice-game.forum.defeat-count')}</span>
            {this.defeatCount}
          </div>
        </div>

        <ul class="ZivenDiceGameList">
          {this.diceGameList.map((diceGameListItem) => {
            if(diceGameListItem.gamePlayedID()===null){
              return (
                <li class="ZivenDiceGameListItems">
                  {DiceGameListItem.component({ diceGameListItem,category:this.category })}
                </li>
              );
            }
          })}
        </ul>

        {!this.loading && this.diceGameList.length===0 && (
          <div>
            <div style="font-size:1.4em;color: var(--muted-more-color);text-align: center;height: 300px;line-height: 100px;">{app.translator.trans("ziven-dice-game.forum.list-empty")}</div>
          </div>
        )}

        {!loading && this.hasMoreResults() && (
          <div style="text-align:center;padding:20px">
            <Button className={'Button Button--primary'} disabled={this.loading} loading={this.loading} onclick={() => this.loadMore()}>
              {app.translator.trans('ziven-dice-game.forum.load-more')}
            </Button>
          </div>
        )}

        {loading && <div className="ZivenDiceGame-loadMore">{loading}</div>}
      </div>
    );
  }

  refresh(){
    this.diceGameList = [];
    this.loading = true;
    m.redraw();
    this.loadResults();
  }

  hasMoreResults() {
    return this.moreResults;
  }

  loadMore() {
    this.loading = true;
    this.loadResults(this.diceGameList.length);
  }

  parseResults(results) {
    this.moreResults = !!results.payload.links && !!results.payload.links.next;
    [].push.apply(this.diceGameList, results);
    this.loading = false;
    m.redraw();

    return results;
  }

  loadResults(offset = 0) {
    return app.store
      .find("zivenDiceGame", {
        page: {
          offset
        },
      })
      .catch(() => {})
      .then(this.parseResults.bind(this));
  }

  loadSummary() {
    return app.store
      .find("zivenDiceGameSummary")
      .catch(() => {})
      .then((result)=>{
        const data = result[0];
        this.winCount = data.winCount();
        this.defeatCount = data.defeatCount();
      });
  }
}

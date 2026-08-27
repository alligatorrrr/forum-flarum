import Component from "flarum/Component";
import Button from 'flarum/components/Button';
import LoadingIndicator from "flarum/components/LoadingIndicator";
import Select from 'flarum/common/components/Select';
import Stream from 'flarum/utils/Stream';

import DiceGameListItem from "./DiceGameListItem";
import DiceGameResultListItem from "./DiceGameResultListItem";

export default class DiceGameRecentGameComponent extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = true;
    this.moreResults = false;
    this.diceGameList = [];
    this.category = this.attrs.category;

    this.filterItemType = Stream(this.category);
    this.itemTypeOption = {
      "recentGame":app.translator.trans('ziven-dice-game.forum.filter-item-recent-game'),
      "recentResult":app.translator.trans('ziven-dice-game.forum.filter-item-recent-result'),
    };
    this.loadResults();
  }

  view() {
    let loading;
    const allowDiceGame = app.forum.attribute("zivenAllowDiceGame");
    const filterItemType = this.filterItemType();

    if(allowDiceGame===false){
      return [];
    }

    if(this.loading){
      loading = LoadingIndicator.component({size: "large"});
    }

    return (
      <div>
        <div className="ZivenDiceGameHeaderWithFilter">
          <Button className={'Button'} disabled={this.loading} onclick={() => m.route.set(app.route('zivenDiceGame'))}>
            {app.translator.trans('ziven-dice-game.forum.return-game-list')}
          </Button>
          <Select
            value={this.filterItemType()}
            disabled={this.loading}
            options={this.itemTypeOption}
            buttonClassName="Button"
            onchange={(e) => {
              this.filterItemType(e);
              this.filterItem(e)
            }}
          />
        </div>

        <ul class="ZivenDiceGameList">
          {this.diceGameList.map((diceGameListItem) => {
            if(filterItemType==="recentResult"){
              return (
                <li class="ZivenDiceGameListItems">
                  {DiceGameResultListItem.component({ diceGameListItem })}
                </li>
              );
            }else{
              if(diceGameListItem.gamePlayedID()===null){
                return (
                  <li class="ZivenDiceGameListItems">
                    {DiceGameListItem.component({ diceGameListItem,category:this.category })}
                  </li>
                );
              }
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

  filterItem(e){
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
    const filterItemType = this.filterItemType();

    if(filterItemType==="recentGame"){
      return app.store
      .find("zivenDiceGame", {
        filter:"recentGame",
        page: {
          offset
        },
      })
      .catch(() => {})
      .then(this.parseResults.bind(this));
    }else if(filterItemType==="recentResult"){
      return app.store
      .find("zivenDiceGameResult", {
        page: {
          offset
        },
      })
      .catch(() => {})
      .then(this.parseResults.bind(this));
    }
  }
}

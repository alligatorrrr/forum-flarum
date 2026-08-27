import Page from 'flarum/components/Page';
import IndexPage from 'flarum/components/IndexPage';
import listItems from 'flarum/common/helpers/listItems';
import DiceGameListComponent from "./DiceGameListComponent";
import DiceGameRecentGameComponent from "./DiceGameRecentGameComponent";

export default class MoneyLeaderboardIndexPage extends Page {
  oninit(vnode) {
    super.oninit(vnode);
    this.titleText = app.translator.trans("ziven-dice-game.forum.dice-game");
    this.categoryList = ["index","recentGame","recentResult"];
    this.categorySelected = "index";
    this.checkParameters();

    app.history.push('zivenDiceGame');
  }

  oncreate(vnode) {
    super.oncreate(vnode);
    app.setTitle(this.titleText);
    app.setTitleCount(0);
  }

  onupdate(){
    $(".item-nav button .Button-label").text(this.titleText);
  }

  checkParameters(){
    const searchParams = new URLSearchParams(window.location.search);
    const tabValue = searchParams.get('t');

    this.categorySelected = (this.categoryList).indexOf(tabValue)===-1?"index":tabValue;
    
    m.redraw();
  }

  view() {
    return (
      <div className="ZivenDiceGamePage">
        {IndexPage.prototype.hero()}

        <div className="container">
          <div className="sideNavContainer">
            <nav className="IndexPage-nav sideNav">
              <ul>{listItems(IndexPage.prototype.sidebarItems().toArray())}</ul>
            </nav>

            <div class="ZivenDiceGameContainer">
              <div class="ZivenDiceGameTitle">
                {app.translator.trans(this.titleText)}
              </div>

              {this.categorySelected==="index" && (
                <DiceGameListComponent category={this.categorySelected} />
              )}

              {(this.categorySelected==="recentGame" || this.categorySelected==="recentResult") && (
                <DiceGameRecentGameComponent category={this.categorySelected} />
              )}
            </div>
          </div>
        </div>
      </div>
    );
  }
}

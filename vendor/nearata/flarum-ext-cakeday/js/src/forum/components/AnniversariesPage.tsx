import AnniversariesState from "../states/AnniversariesState";
import AnniversariesList from "./AnniversariesList";
import Button from "flarum/common/components/Button";
import Dropdown from "flarum/common/components/Dropdown";
import Page from "flarum/common/components/Page";
import listItems from "flarum/common/helpers/listItems";
import ItemList from "flarum/common/utils/ItemList";
import extractText from "flarum/common/utils/extractText";
import app from "flarum/forum/app";
import IndexPage from "flarum/forum/components/IndexPage";
import type Mithril from "mithril";

export default class AnniversariesPage extends Page {
  // @ts-ignore
  state!: AnniversariesState;

  oninit(vnode: Mithril.Vnode<this>): void {
    super.oninit(vnode);

    this.state = new AnniversariesState();
    this.state.loadMore();
  }

  oncreate(vnode: Mithril.VnodeDOM<this>): void {
    super.oncreate(vnode);

    app.setTitle(
      extractText(app.translator.trans("nearata-cakeday.forum.page.title"))
    );
    app.setTitleCount(0);
  }

  view(vnode: Mithril.Vnode<this>) {
    return (
      <div className="IndexPage NearataCakeday">
        {IndexPage.prototype.hero()}
        <div className="container">
          <div className="sideNavContainer">
            <nav className="IndexPage-nav sideNav">
              <ul>{listItems(IndexPage.prototype.sidebarItems().toArray())}</ul>
            </nav>
            <div className="IndexPage-results sideNavOffset">
              <div className="IndexPage-toolbar">
                <ul className="IndexPage-toolbar-view">
                  {listItems(this.viewItems().toArray())}
                </ul>
                <ul className="IndexPage-toolbar-action">
                  {listItems(this.actionItems().toArray())}
                </ul>
              </div>
              <AnniversariesList state={this.state} />
            </div>
          </div>
        </div>
      </div>
    );
  }

  viewItems() {
    const items = new ItemList();

    items.add(
      "filter",
      <Dropdown
        buttonClassName="Button"
        label={this.state.filterOptions[this.state.currentFilter]}
      >
        {Object.entries(this.state.filterOptions).map(([k, v]) => {
          const active = this.state.currentFilter === k;

          return (
            <Button
              className="Button"
              icon={active ? "fas fa-check" : true}
              onclick={() => this.state.refresh(k)}
            >
              {v}
            </Button>
          );
        })}
      </Dropdown>
    );

    return items;
  }

  actionItems() {
    const items = new ItemList();

    items.add(
      "refresh",
      <Button
        className="Button Button--icon"
        aria-label={app.translator.trans("core.forum.index.refresh_tooltip")}
        icon="fas fa-sync"
        onclick={() => this.state.refresh()}
      />
    );

    return items;
  }
}

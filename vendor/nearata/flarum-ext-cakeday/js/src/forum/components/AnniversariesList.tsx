import AnniversariesState from "../states/AnniversariesState";
import AnniversariesListItem from "./AnniversariesListItem";
import Component from "flarum/common/Component";
import Button from "flarum/common/components/Button";
import LoadingIndicator from "flarum/common/components/LoadingIndicator";
import Placeholder from "flarum/common/components/Placeholder";
import app from "flarum/forum/app";
import type Mithril from "mithril";

export default class AnniversariesList extends Component {
  // @ts-ignore
  state!: AnniversariesState;

  oninit(vnode: Mithril.Vnode<this>): void {
    super.oninit(vnode);

    this.state = vnode.attrs.state;
  }

  oncreate(vnode: Mithril.VnodeDOM<this>): void {
    super.oncreate(vnode);
  }

  view(vnode: Mithril.Vnode<this>) {
    return (
      <div className="AnniversariesList">
        <h2>{this.state.getH2()}</h2>
        <div className="users">
          {this.state.users.map((val) => {
            return <AnniversariesListItem user={val} />;
          })}
        </div>
        {!this.state.loading && !this.state.users.length && (
          <Placeholder
            text={app.translator.trans("nearata-cakeday.forum.page.empty")}
          />
        )}
        {this.state.loading && !this.state.users.length && <LoadingIndicator />}
        {this.state.hasMoreResults && (
          <div className="loadMore">
            <Button
              className="Button"
              onclick={() => this.state.loadMore()}
              loading={this.state.loading}
              disabled={this.state.loading}
            >
              {app.translator.trans(
                "nearata-cakeday.forum.page.load_more_button"
              )}
            </Button>
          </div>
        )}
      </div>
    );
  }
}

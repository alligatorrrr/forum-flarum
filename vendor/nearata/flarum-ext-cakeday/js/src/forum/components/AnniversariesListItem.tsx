import Component from "flarum/common/Component";
import avatar from "flarum/common/helpers/avatar";
import username from "flarum/common/helpers/username";
import User from "flarum/common/models/User";
import humanTime from "flarum/common/utils/humanTime";
import type Mithril from "mithril";

export default class AnniversariesListItem extends Component {
  user!: User;

  oninit(vnode: Mithril.Vnode<this>): void {
    super.oninit(vnode);

    this.user = vnode.attrs.user;
  }

  oncreate(vnode: Mithril.VnodeDOM<this>): void {
    super.oncreate(vnode);
  }

  view(vnode: Mithril.Vnode<this>) {
    return (
      <div className="AnniversariesListItem">
        <div className="avatar">{avatar(this.user)}</div>
        <div className="details">
          <div class="username">{username(this.user)}</div>
          <div class="joined">{humanTime(this.user.joinTime())}</div>
        </div>
      </div>
    );
  }
}

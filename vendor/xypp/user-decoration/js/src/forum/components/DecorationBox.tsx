import Component, { ComponentAttrs } from 'flarum/common/Component';
import Mithril, { Attributes } from 'mithril';
import app from 'flarum/forum/app';
import { userElementInfo } from '../../common/type';
import { StyleFetcher } from '../../common/data/styleFetcher';
import Button from 'flarum/common/components/Button';
import UserDecorations from '../../common/models/UserDecorations';
import CreateDecorationModal from '../../forum/components/CreateDecorationModal';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import username from 'flarum/common/helpers/username';
import UserCard from 'flarum/forum/components/UserCard';
import { avatarColor } from '../../common/utils/DecorationHijack';
import { generatePost } from '../utils/fakePost';
import { applyDecorationOn } from '../../common/utils/DecorationApplier';
import { showIf } from "../utils/nodeUtil"
export default class DecorationBox<Attrs extends ComponentAttrs = ComponentAttrs> extends Component {
  type: string = '';
  id: number = 0;
  isCurrent: boolean = false;
  uodId: number = 0;
  decoration: UserDecorations | null = null;
  changing = false;
  oninit(vnode: Mithril.Vnode<Attrs, this>) {
    super.oninit(vnode);
  }

  oncreate(vnode: Mithril.Vnode<Attrs, this>) {
    super.oncreate(vnode);
  }

  onupdate(vnode: Mithril.Vnode<Attrs, this>) {
    super.onupdate(vnode);
  }
  async change() {
    this.changing = true;
    m.redraw();
    const toSave: Record<string, string | null> = {};
    if (this.isCurrent) {
      toSave[`${this.type}_decoration`] = 'null';
    } else {
      toSave[`${this.type}_decoration`] = this.decoration!.id() as string;
    }
    await app.session.user!.save(toSave);
    this.changing = false;
    m.redraw();
  }
  view() {
    this.id = (this.attrs as any)['decoration_id'];
    this.type = (this.attrs as any)['type'];
    this.uodId = (this.attrs as any)['user_own_decoration_id'];
    if (!["avatar", "name", "card", "post"].includes(this.type)) {
      this.type = 'error';
    }

    let content: any = <div class="error">{app.translator.trans('xypp-user-decoration.forum.decoration-box.error')}</div>;

    let decorationObj: userElementInfo = {
      id: parseInt(app.session.user!.id() || ''),
      username: app.session.user!.username(),
      decorationId: this.id,
      //@ts-ignore
      color: app.session.user!.hijackColor().replace(/\#/g, "@")
    };
    this.decoration = app.store.getById('user-decorations', decorationObj.decorationId + '') as UserDecorations;
    if (!this.decoration) {
      StyleFetcher.getInstance()
        ?.fetchStyle(this.id)
        .then(() => m.redraw());
    }



    if (this.changing) {
      content = <LoadingIndicator></LoadingIndicator>;
    } else if (this.type == 'avatar') {
      if (!app.forum.attribute("username_hijack")) {
        content = app.translator.trans('xypp-user-decoration.forum.decoration-box.closed');
      } else {
        content = <img title="-" class="Avatar"
          src={
            //@ts-ignore
            (app.session.user?.realAvatarUrl() || '') + '#' + JSON.stringify(decorationObj)
          }
        />;
      }
    } else if (this.type == 'name') {
      if (!app.forum.attribute("username_hijack")) {
        content = app.translator.trans('xypp-user-decoration.forum.decoration-box.closed');
      } else {
        content = (<span class="username-container username"><span class="username-text">{
          //@ts-ignore
          app.session.user?.realUserName()
        }</span></span>);
        if (this.decoration) applyDecorationOn(content, this.decoration);
      }
    } else if (this.type == "card") {
      content = <div class="decoration-box-card-container">
        <UserCard user={app.session.user} controlsButtonClassName="UserCard-controls App-primaryControl" className="UserCard--popover in" decoration_id={this.decoration?.id()}></UserCard>
      </div>

    } else if (this.type == "post") {
      const afterDecoration = generatePost(app.session.user!);
      if (this.decoration)
        applyDecorationOn(afterDecoration, this.decoration);
      content = <div class="decoration-box-card-container">{afterDecoration}</div>
    }

    this.isCurrent = this.decoration && app.session.user?.attribute(`${this.type}_decoration`) == this.decoration?.id();

    return (
      <div className="DecorationBox">
        <div className='decoration-head'>
          <div className='decoration-name'>{this.decoration?.name()}</div>
          <div className='decoration-type'>{app.translator.trans('xypp-user-decoration.forum.decoration-box.' + this.type)}</div>
        </div>
        <div className='prev-warpper'>
          {content}
        </div>
        <div class="decoration-box-content">
          {this.decoration ? this.decoration!.desc() : app.translator.trans('xypp-user-decoration.forum.decoration-box.loading')}
        </div>
        {// 变更装扮按钮
          showIf(!((this.attrs as any).noBtn),
            <Button class="Button Button--primary" loading={this.changing} disabled={this.changing} onclick={this.change.bind(this)}>
              {this.isCurrent
                ? app.translator.trans('xypp-user-decoration.forum.decoration.remove_button')
                : app.translator.trans('xypp-user-decoration.forum.decoration.change_button')}
            </Button>
          )}
        {// 删除按钮（右上角）
          showIf(!((this.attrs as any).noDelete || !this.uodId),
            <div class="delete-decoration" onclick={this.delete.bind(this)}>
              <i class="fas fa-times" aria-label={app.translator.trans('xypp-user-decoration.forum.decoration.delete_button')}></i>
            </div>
          )}
        {// 编辑按钮（左上角）
          showIf(!((this.attrs as any).noEdit),
            <div class="edit-decoration" onclick={this.edit.bind(this)}>
              <i class="fas fa-edit" aria-label={app.translator.trans('xypp-user-decoration.forum.decoration.edit_button')}></i>
            </div>
          )}
      </div>
    );
  }

  async delete() {
    if (confirm(app.translator.trans('xypp-user-decoration.forum.decoration.delete_confirm') as string)) {
      this.changing = true;
      await app.request({
        url: app.forum.attribute('apiUrl') + '/user-own-decoration/' + this.uodId + '/delete',
      });
      this.changing = false;

      setRouteWithForcedRefresh(app.route("user.user_own_decoration", { username: app.current.get("user").attribute("slug") }));
    }
  }

  edit() {
    app.modal.show(CreateDecorationModal, { decoration_id: this.decoration!.id() });
  }
}

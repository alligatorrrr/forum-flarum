import Modal from 'flarum/common/components/Modal';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import UserOwnDecoration from '../../common/models/UserOwnDecoration';
import DecorationBox from './DecorationBox';
import UserDecorations from '../../common/models/UserDecorations';
import { StyleFetcher } from '../../common/data/styleFetcher';
import LinkButton from 'flarum/common/components/LinkButton';
import { reloadStyleElement } from '../../common';
export default class CreateDecorationModal extends Modal {
  loading = false;
  decorationId: string = '';
  decoration: UserDecorations | null = null;

  className() {
    return 'Modal--small';
  }

  title() {
    if ((this.attrs as any).decoration_id) {
      return app.translator.trans("xypp-user-decoration.forum.create-modal.edit-title", [(this.attrs as any).decoration_id] as any)
    }
    return app.translator.trans('xypp-user-decoration.forum.create-modal.title');
  }

  content() {
    const that = this;
    return (
      <div className="Modal-body">
        <div className="Form">
          <div className="Form-group">
            <label for="xypp-user-decoration-create-ipt-type">{app.translator.trans('xypp-user-decoration.forum.create-modal.type.title')}</label>
            <Select
              id="xypp-user-decoration-create-ipt-type"
              options={{
                avatar: app.translator.trans('xypp-user-decoration.forum.create-modal.type.avatar'),
                name: app.translator.trans('xypp-user-decoration.forum.create-modal.type.username'),
                card: app.translator.trans('xypp-user-decoration.forum.create-modal.type.usercard'),
                post: app.translator.trans('xypp-user-decoration.forum.create-modal.type.post'),
              }}
            ></Select>
          </div>
          <div className="Form-group">
            <label for="xypp-user-decoration-create-ipt-name">{app.translator.trans('xypp-user-decoration.forum.create-modal.name')}</label>
            <input id="xypp-user-decoration-create-ipt-name" required className="FormControl" step="any" />
          </div>
          <div className="Form-group">
            <label for="xypp-user-decoration-create-ipt-desc">{app.translator.trans('xypp-user-decoration.forum.create-modal.desc')}</label>
            <textarea id="xypp-user-decoration-create-ipt-desc" required className="FormControl" step="any"></textarea>
          </div>
          <p>{app.translator.trans('xypp-user-decoration.forum.create-modal.tip')}</p>
          <div className="Form-group">
            <label for="xypp-user-decoration-create-ipt-style">{app.translator.trans('xypp-user-decoration.forum.create-modal.style')}</label>
            <textarea id="xypp-user-decoration-create-ipt-style" required className="FormControl" step="any"></textarea>
          </div>
          <div className="Form-group">
            <Button class="Button Button--primary" type="submit" loading={this.loading} disabled={this.loading}>
              {(this.attrs as any).decoration_id ? app.translator.trans('xypp-user-decoration.forum.create-modal.edit-button') : app.translator.trans('xypp-user-decoration.forum.create-modal.button')}
            </Button>
            {(this.attrs as any).decoration_id ? (
              <LinkButton loading={this.loading} disabled={this.loading} onclick={this.delete.bind(this)}>
                <i class="fas fa-trash"></i>{app.translator.trans('xypp-user-decoration.forum.create-modal.delete-button')}
              </LinkButton>) : ""
            }
          </div>
        </div>
      </div>
    );
  }
  onready() {
    this.decorationId = (this.attrs as any).decoration_id;
    if (this.decorationId) {
      this.loading = true;
      StyleFetcher.getInstance()
        ?.fetchStyle(this.decorationId)
        .then((style: any) => {
          this.decoration = style;
          this.loading = false;
          this.$('#xypp-user-decoration-create-ipt-name').val(style.name() as string);
          this.$('#xypp-user-decoration-create-ipt-desc').val(style.desc() as string);
          this.$('#xypp-user-decoration-create-ipt-style').val(style.style() as string);
          this.$('#xypp-user-decoration-create-ipt-type').val(style.type() as string);
          m.redraw();
        });
    }
  }
  async onsubmit(e: any) {
    e.preventDefault();
    this.loading = true;
    try {
      const payload = await app.request<any>({
        url: app.forum.attribute('apiUrl') + '/user_decoration',
        method: 'POST',
        body: {
          attributes: {
            id: this.decorationId,
            style: this.$('#xypp-user-decoration-create-ipt-style').val(),
            desc: this.$('#xypp-user-decoration-create-ipt-desc').val(),
            name: this.$('#xypp-user-decoration-create-ipt-name').val(),
            type: this.$('#xypp-user-decoration-create-ipt-type').val(),
          },
        },
      });
      app.store.pushPayload(payload);
      reloadStyleElement(parseInt(this.decorationId));
      app.modal.close();
      app.alerts.show({ type: 'success' }, app.translator.trans('xypp-user-decoration.forum.create-success'));

      setRouteWithForcedRefresh(app.route("user.user_own_decoration", { username: app.current.get("user").attribute("slug") }));
    } catch (e: any) {
      this.loading = false;
    }
  }
  async delete() {
    if (!confirm(app.translator.trans('xypp-user-decoration.forum.delete_confirm') as string)) return;
    this.loading = true;
    try {
      await app.request({
        url: app.forum.attribute('apiUrl') + '/user_decoration/' + (this.attrs as any).decoration_id + "/delete",
        method: 'GET',
      });
      app.modal.close();
      app.alerts.show({ type: 'success' }, app.translator.trans('xypp-user-decoration.forum.delete-success'));

      setRouteWithForcedRefresh(app.route("user.user_own_decoration", { username: app.current.get("user").attribute("slug") }));
    } catch (e: any) {
      this.loading = false;
    }
  }
}

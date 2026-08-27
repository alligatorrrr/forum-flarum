import Modal from 'flarum/common/components/Modal';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import UserOwnDecoration from '../../common/models/UserOwnDecoration';
import DecorationBox from '../components/DecorationBox';
import UserDecorations from 'src/common/models/UserDecorations';
export default class OfferDecorationModal extends Modal {
    loading = true;
    records: Record<number, string> = {};
    value: number = 0;
    decorationBox: DecorationBox | null = null;
    decorationType: string = '';
    decorationId: string = '';
    className() {
        return 'Modal--small';
    }

    title() {
        return app.translator.trans('xypp-user-decoration.forum.modal.title');
    }

    content() {
        const that = this;
        return (
            <div className="Modal-body">
                <p>{app.translator.trans('xypp-user-decoration.forum.modal.tip')}</p>
                <Select
                    options={this.records}
                    value={this.value}
                    onchange={this.change.bind(this)}
                ></Select>
                <DecorationBox
                    noBtn={true}
                    oncreate={(e) => (that.decorationBox = e as any)}
                    decoration_id={that.decorationId}
                    type={that.decorationType}
                ></DecorationBox>
                <div className="paymodal-btn">
                    <Button class="Button Button--primary" loading={this.loading} disabled={this.loading} onclick={this.offer.bind(this)}>
                        {app.translator.trans('xypp-user-decoration.forum.modal.button')}
                    </Button>
                </div>
            </div>
        );
    }
    onready() {
        this.loading = true;
        app.store.find('user_decoration_all').then(() => {
            this.records = {};
            app.store.all('user-decorations').forEach((decoration) => {
                this.records[parseInt(decoration.id() as string)] =
                    '[' +
                    app.translator.trans('xypp-user-decoration.forum.decoration-box.' + decoration.attribute('type')) +
                    ']' +
                    decoration.attribute('name') +
                    ':' +
                    decoration.attribute('desc');
            });
            this.loading = false;
            m.redraw();
        });
    }
    async offer() {
        this.loading = true;
        try {
            await app.request({
                url: app.forum.attribute('apiUrl') + '/user-own-decoration',
                method: 'POST',
                body: {
                    attributes: {
                        user_id: (this.attrs as any).user_id,
                        decoration_id: this.decorationId,
                    },
                },
            });
            app.modal.close();


            setRouteWithForcedRefresh(app.route("user.user_own_decoration", { username: app.current.get("user").attribute("slug") }));
        } catch (e: any) {
            this.loading = false;
        }
    }
    change(e:any) {
        this.value = e;
        const dec: UserDecorations = app.store.getById('user-decorations', e)!;
        this.decorationType = dec.type() as string;
        this.decorationId = dec.id() as string;
        m.redraw();
    }
}

import Mithril from 'mithril';
import UserPage from 'flarum/forum/components/UserPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import app from 'flarum/forum/app';
import UserOwnDecoration from '../../common/models/UserOwnDecoration';
import DecorationBox from './DecorationBox';
import Button from 'flarum/common/components/Button';
import CreateDecorationModal from './CreateDecorationModal';
import User from 'flarum/common/models/User';
import { showIf } from '../utils/nodeUtil';
import Placeholder from 'flarum/common/components/Placeholder';
export class DecorationPage extends UserPage {
    loading: boolean = false;
    record: UserOwnDecoration[] | null = null;
    oninit(vnode: Mithril.Vnode<any, this>): void {
        super.oninit(vnode);
        this.loadUser(m.route.param('username'));
    }
    oncreate(vnode: Mithril.VnodeDOM<any, this>): void {
    }
    onupdate(vnode: Mithril.VnodeDOM<any, this>): void {

    }
    show(user: User): void {
        super.show(user);
        this.loadData();
    }
    content(): JSX.Element {
        return (
            <div className="decoration-page-container">
                <div class="decoration-page-title">
                    <h3>{app.translator.trans('xypp-user-decoration.forum.decorations')}</h3>
                    {(app.session.user as any).canCreateDecoration() ? (
                        <Button class="Button Button--primary" onclick={this.create.bind(this)}>
                            <i class="fas fa-plus" />
                            <span>{app.translator.trans('xypp-user-decoration.forum.create')}</span>
                        </Button>
                    ) : (
                        ''
                    )}
                </div>

                <div className="decoration-page">
                    {showIf(this.loading, (
                        <LoadingIndicator display="block" />
                    ), showIf(!!(this.record?.length),
                        this.record?.map((item, index) => {
                            return (
                                <DecorationBox
                                    type={item.decoration_type()}
                                    user_own_decoration_id={(!item.attribute("fake")) && item.id()}
                                    decoration_id={item.decoration_id()}
                                    noBtn={this.user?.id() != app.session.user?.id()}
                                    noDelete={!(app.session.user as any)?.canDeleteDecoration()}
                                    noEdit={!(app.session.user as any).canCreateDecoration()}
                                />
                            );
                        }), (<Placeholder text={app.translator.trans("xypp-user-decoration.forum.decoration-no-available")} />)
                    ))}
                </div>
            </div>
        );
    }
    async loadData() {
        if (this.record != null) return;
        if (!this.user?.id()) return;
        if (this.loading) return;
        this.loading = true;
        app.store.all("user-own-decoration").forEach(function (m) { app.store.remove(m); });
        m.redraw();
        const payload: any = await app.request({
            url: app.forum.attribute('apiUrl') + '/user-own-decoration?id=' + this.user?.id(),
            method: 'GET',
        });
        let i = 1;
        payload.data.forEach((element: any) => i = Math.max(i, element.id || 0));
        payload.data.forEach((element: any) => {
            element.attributes.fake = !(element.id);
            element.id = element.id || (++i);
        });
        this.record = app.store.pushPayload(payload) as any;
        this.loading = false;
        m.redraw();
    }
    create() {
        app.modal.show(CreateDecorationModal);
    }
}

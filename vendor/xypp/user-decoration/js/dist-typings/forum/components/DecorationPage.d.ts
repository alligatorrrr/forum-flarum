import Mithril from 'mithril';
import UserPage from 'flarum/forum/components/UserPage';
import UserOwnDecoration from '../../common/models/UserOwnDecoration';
import User from 'flarum/common/models/User';
export declare class DecorationPage extends UserPage {
    loading: boolean;
    record: UserOwnDecoration[] | null;
    oninit(vnode: Mithril.Vnode<any, this>): void;
    oncreate(vnode: Mithril.VnodeDOM<any, this>): void;
    onupdate(vnode: Mithril.VnodeDOM<any, this>): void;
    show(user: User): void;
    content(): JSX.Element;
    loadData(): Promise<void>;
    create(): void;
}

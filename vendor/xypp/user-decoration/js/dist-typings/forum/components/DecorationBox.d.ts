import Component, { ComponentAttrs } from 'flarum/common/Component';
import Mithril from 'mithril';
import UserDecorations from '../../common/models/UserDecorations';
export default class DecorationBox<Attrs extends ComponentAttrs = ComponentAttrs> extends Component {
    type: string;
    id: number;
    isCurrent: boolean;
    uodId: number;
    decoration: UserDecorations | null;
    changing: boolean;
    oninit(vnode: Mithril.Vnode<Attrs, this>): void;
    oncreate(vnode: Mithril.Vnode<Attrs, this>): void;
    onupdate(vnode: Mithril.Vnode<Attrs, this>): void;
    change(): Promise<void>;
    view(): JSX.Element;
    delete(): Promise<void>;
    edit(): void;
}

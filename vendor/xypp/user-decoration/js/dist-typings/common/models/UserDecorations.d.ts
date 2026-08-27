import Model from 'flarum/common/Model';
export default class UserDecorations extends Model {
    style: () => unknown;
    type: () => unknown;
    createdAt: () => Date | null | undefined;
    name: () => unknown;
    desc: () => unknown;
}

import Model from 'flarum/common/Model';
export default class UserOwnDecoration extends Model {
    user_id: () => unknown;
    decoration_id: () => unknown;
    decoration_type: () => unknown;
}

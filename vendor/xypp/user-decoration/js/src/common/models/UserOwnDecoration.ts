import Model from 'flarum/common/Model';

// For more details about frontend models
// checkout https://docs.flarum.org/extend/models.html#frontend-models

export default class UserOwnDecoration extends Model {
  user_id = Model.attribute('user_id');
  decoration_id = Model.attribute('decoration_id');
  decoration_type = Model.attribute('type');
}

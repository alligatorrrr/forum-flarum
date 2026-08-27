import Model from 'flarum/common/Model';

// For more details about frontend models
// checkout https://docs.flarum.org/extend/models.html#frontend-models

export default class UserDecorations extends Model {
  style = Model.attribute('style');
  type = Model.attribute("type");
  createdAt = Model.attribute('createdAt', Model.transformDate);
  name = Model.attribute('name');
  desc = Model.attribute('desc');
}

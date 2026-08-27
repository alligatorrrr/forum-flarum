import Model from 'flarum/common/Model';

// For more details about frontend models
// checkout https://docs.flarum.org/extend/models.html#frontend-models

export default class MediaInfo extends Model {
  createdAt = Model.attribute('createdAt', Model.transformDate);
  updatedAt = Model.attribute('updatedAt', Model.transformDate);
  title = Model.attribute('title');
  url = Model.attribute('url');
  poster = Model.attribute('poster');
  desc = Model.attribute('desc');
  source = Model.attribute('source');
  post = Model.hasOne('post');
}

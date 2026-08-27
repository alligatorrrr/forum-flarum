import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
import extendUserPreferences from './User/extendUserPreferences';
import Notification from './User/Notification';

export default function () {
  User.prototype.canViewWhoTypes = Model.attribute('canViewWhoTypes');

  Notification();
  extendUserPreferences();
}

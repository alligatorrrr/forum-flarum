import Extend from 'flarum/common/extenders';
import UserDecorations from "./models/UserDecorations"
import UserOwnDecoration from './models/UserOwnDecoration';
import User from 'flarum/common/models/User';
export default [
  new Extend.Store()
    .add('user-decorations', UserDecorations)
  ,
  new Extend.Store()
    .add('user-own-decoration', UserOwnDecoration),
  new Extend.Model(User)
    .attribute('avatar_decoration'),
];
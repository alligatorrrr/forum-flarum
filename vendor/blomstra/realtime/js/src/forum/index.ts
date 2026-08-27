import app from 'flarum/forum/app';
import Application from './extend/Application';
import Discussion from './extend/Discussion';
import DiscussionList from './extend/DiscussionList';
import Post from './extend/Post';
import User from './extend/User';

app.initializers.add('blomstra-realtime', () => {
  Application();
  Discussion();
  DiscussionList();
  Post();
  User();
});

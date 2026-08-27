import app from 'flarum/forum/app';
import NewActivity from './DiscussionList/NewActivity';

export default function DiscussionList() {
  if (!!app.data['blomstra-realtime.release-discussion-updates']) {
    NewActivity();
  }
}

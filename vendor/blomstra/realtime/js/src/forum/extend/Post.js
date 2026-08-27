import app from 'flarum/forum/app';
import Flagged from './Post/Flagged';

export default function () {
  if (app.initializers.has('flarum-flags')) {
    Flagged();
  }
}

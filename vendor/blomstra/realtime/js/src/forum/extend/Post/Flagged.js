import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import FlagsDropdown from 'flarum/flags/components/FlagsDropdown';

export default function () {
  extend(FlagsDropdown.prototype, 'oninit', () => {
    if (app.websocket_channels.user) {
      app.websocket_channels.user.bind('flagged', (data) => {
        app.session.user = app.store.pushPayload(data);

        app.forum.pushAttributes({ flagCount: app.session.user.attribute('newFlagCount') });
        app.flags.cache = null;

        m.redraw();
      });
    }
  });
}

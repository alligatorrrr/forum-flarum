import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Pusher from 'pusher-js';
import Application from 'flarum/common/Application';

export default function () {
  extend(Application.prototype, 'mount', function () {
    // Enable logging to console when debug is enabled.
    Pusher.logToConsole = this.forum.attribute('debug');

    const wsHost = this.forum.attribute('websocket.host');
    const secure = this.forum.attribute('websocket.secure');

    app.websocket = new Pusher(this.forum.attribute('websocket.key'), {
      channelAuthorization: {
        endpoint: this.forum.attribute('apiUrl') + '/websocket/auth',
      },
      wsHost,
      wsPort: this.forum.attribute('websocket.port'),
      wssPort: this.forum.attribute('websocket.port'),
      enabledTransports: ['wss', 'ws'],
      forceTLS: secure,
    });

    app.websocket_channels = {
      public: null,
      user: null,
    };

    if (app.session.user) {
      app.websocket_channels.user = app.websocket.subscribe('private-user=' + app.session.user.id());
    } else if (!this.forum.attribute('websocket.disallow_connection')) {
      app.websocket_channels.public = app.websocket.subscribe('public');
    }
  });
}

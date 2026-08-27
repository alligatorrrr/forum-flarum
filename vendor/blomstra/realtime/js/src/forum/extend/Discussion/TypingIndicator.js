import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Stream from 'flarum/common/utils/Stream';
import throttle from 'lodash-es/throttle';
import icon from 'flarum/common/helpers/icon';
import classList from 'flarum/common/utils/classList';
import PostStream from 'flarum/forum/components/PostStream';

export default function () {
  extend(PostStream.prototype, 'endItems', function (items) {
    if (this.discussion.attribute('canViewWhoTypes')) {
      const typingUsers = Object.keys(this.getTypingUsers());

      const count = typingUsers.length;
      const max = 3;

      const classes = classList(['TypingUsersContainer', count > 0 && 'TypingUsersContainer-active']);
      const typingIcon = count > 0 ? 'fas fa-ellipsis-h fa-beat' : 'fas fa-pause';

      const namedUsers = typingUsers.slice(0, max).join(', ');

      let showUsers = true; // default value

      if (app.session?.user) {
        showUsers = app.session.user.preferences()['blomstra-realtime.typing-indicator-full'];
      }

      items.add(
        'usersTyping',
        <div className={classes} key="typing">
          <div className="TypingUsers">
            {icon(typingIcon)}
            {count > 0
              ? showUsers
                ? app.translator.trans('blomstra-realtime.forum.typing-indicator.users-are-typing', {
                    users: namedUsers,
                    count: count,
                    others: Math.max(count - max, 0),
                  })
                : app.translator.trans('blomstra-realtime.forum.typing-indicator.people-are-typing', { number: count })
              : app.translator.trans('blomstra-realtime.forum.typing-indicator.no-activity')}
          </div>
        </div>,
        70
      );
    }
  });

  PostStream.prototype.getTypingUsers = function () {
    const invalidateWhen = new Date().getTime() - 6000;

    let users = this.usersTyping();
    let timeout = null;

    for (const displayName in users) {
      const time = users[displayName];

      if (time < invalidateWhen) {
        delete users[displayName];
      } else if (!timeout || timeout < time) {
        timeout = time;
      }
    }

    this.usersTyping(users);

    if (timeout && this.typingTruncationListener) {
      clearTimeout(this.typingTruncationListener);
    }

    if (timeout) {
      this.typingTruncationListener = setTimeout(
        function () {
          m.redraw();
        }.bind(this),
        timeout - new Date().getTime()
      );
    }

    return users;
  };

  PostStream.prototype.userTyping = function (data) {
    let users = this.usersTyping();

    if (!data.discloseOnline) {
      data.displayName = app.translator.trans('blomstra-realtime.forum.typing-indicator.anonymous-user');
    }

    users[data.displayName] = data.time;

    this.usersTyping(users);

    m.redraw();
  };

  PostStream.prototype.actorIsTyping = function () {
    const discloseOnline = app.session.user.preferences()?.discloseOnline;

    app.websocket_channels.discussion.trigger('client-typing', {
      displayName: discloseOnline ? app.session.user.displayName() : '[anonymous]',
      discloseOnline,
      time: Date.now(),
    });
  };

  PostStream.prototype.checkTyping = function () {
    if (!app.composer.composingReplyTo(this.discussion)) return;

    if (this.previousContent() !== app.composer.fields.content()) {
      this.actorIsTyping();

      // Update previous, so we can match on the next tick.
      this.previousContent(app.composer.fields.content());
    }
  };

  extend(PostStream.prototype, 'oninit', function (vnode) {
    this.previousContent = new Stream('');
    this.usersTyping = new Stream({});
    this.typingTruncationListener = null;
    this.typingListener = null;
  });

  extend(PostStream.prototype, 'view', function () {
    if (app.forum.attribute('websocket.disallow_connection')) return;

    if (this.discussion && app.composer.editor && !this.typingListener) {
      this.typingListener = throttle(
        function () {
          this.checkTyping();
        }.bind(this),
        2000
      );

      this.typingListener = setInterval(this.typingListener, 1000);
    }

    if (this.discussion) {
      app.websocket_channels.discussion = app.websocket.subscribe('private-typing=' + m.route.param('id').match(/[0-9]+/));

      if (this.discussion.attribute('canViewWhoTypes')) {
        app.websocket_channels.discussion.bind('client-typing', (data) => {
          this.userTyping(data);
        });
      }
    }
  });

  extend(PostStream.prototype, 'onremove', function () {
    if (this.typingListener) this.typingListener.cancel;
    if (this.typingTruncationListener) clearTimeout(this.typingTruncationListener);
  });
}

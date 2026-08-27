import { extend } from 'flarum/extend';
import CommentPost from 'flarum/common/components/CommentPost';
import BadgeModal from './components/BadgeModal';
import UserBadge from '../common/components/UserBadge';

export default function addDiscussionUserBadge() {
  extend(CommentPost.prototype, 'content', function (list) {
    if (this.attrs.post.data.relationships.user == null) return;
    // console.log(this.attrs.post.data.relationships.user);
    // console.log(this.attrs.post.data.relationships.user.data);
    // console.log(this.attrs.post.data.relationships.user.data.id);
    const userID = this.attrs.post.data.relationships.user.data.id;
    const user = this.attrs.post.store.data.users[userID];
    if (!app.forum.attribute('showBadgesInDiscussion') || !user || !user.userBadges) return;
    let userBadges = user.userBadges() ?? [];
    if (userBadges == false) {
      userBadges = [];
    }
    const limit = app.forum.attribute('numberOfBadgesOnUserCard');
    let visibleBadges = userBadges.filter((userBadge) => {
      return userBadge.inUserCard();
    });

    if (visibleBadges.length === 0) {
      return;
      visibleBadges = userBadges.slice(0, limit);
    }

    const badges = visibleBadges.map((userBadge) => {
      return (
        <UserBadge
          badge={userBadge.badge()}
          onclick={() =>
            app.modal.show(BadgeModal, {
              badge: userBadge.badge(),
              userBadgeData: userBadge,
            })
          }
        />
      );
    });

    if (badges.length > 0) {
      list[0].children.push(<div style="height:15px"></div>);
      for (let i = 0; i < badges.length; i++) {
        list[0].children.push(badges[i]);
      }
    }
  });
}

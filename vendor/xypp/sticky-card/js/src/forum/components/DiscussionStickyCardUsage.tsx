import app from 'flarum/forum/app';
import EventPost from 'flarum/forum/components/EventPost';

export default class DiscussionStickyCardUsage extends EventPost {
    icon() {
        return (this.attrs.post.content() as any).takeEffect ? "fas fa-hand-point-up" : "fas fa-hourglass-end";
    }

    descriptionKey() {
        return (this.attrs.post.content() as any).takeEffect
            ? 'xypp-sticky-card.forum.sticky_card_take_effect'
            : 'xypp-sticky-card.forum.sticky_card_expire';
    }
}
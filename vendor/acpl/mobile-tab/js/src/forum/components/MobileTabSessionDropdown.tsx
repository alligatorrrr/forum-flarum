import app from 'flarum/forum/app';
import SessionDropdown from 'flarum/forum/components/SessionDropdown';
import avatar from 'flarum/common/helpers/avatar';
import LinkButton from 'flarum/common/components/LinkButton';
import ItemList from 'flarum/common/utils/ItemList';

export default class MobileTabSessionDropdown extends SessionDropdown {
  getButtonContent() {
    const user = app.session.user;
    return [avatar(user), ' ', <span className="Button-label">{app.translator.trans('acpl-mobile-tab.forum.profile')}</span>];
  }

  items() {
    // 调用父类的 items() 方法来获取默认的菜单项
    const items = super.items();

    // 添加新的 "文库/文集" 项
    items.add(
      'bookmark-discussions-list',
      <LinkButton
        href={`${app.route('user', { username: app.session.user.slug() })}/discussion-lists`}
        icon="fas fa-list-ol"
        className="Button Button--link"
      >
        坛列/文集
      </LinkButton>,
      20 // 优先级
    );

    // 添加新的 "收藏" 项
    items.add(
      'bookmark-discussions',
      <LinkButton
        href="/bookmarked-posts"
        icon="fas fa-bookmark"
        className="Button Button--link"
      >
        书签
      </LinkButton>,
      15 // 优先级
    );

    return items;
  }
}
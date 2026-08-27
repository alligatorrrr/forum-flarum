import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import ItemList from 'flarum/common/utils/ItemList';
import SettingsPage from 'flarum/forum/components/SettingsPage';
import FieldSet from 'flarum/common/components/FieldSet';
import type Mithril from 'mithril';
import RealtimeUserPreferencesItems from './RealtimeUserPreferences';

export default function extendUserPreferences() {
  if (!!app.data['blomstra-realtime.typing-indicator']) {
    extend(SettingsPage.prototype, 'settingsItems', function (items: ItemList<Mithril.Children>) {
      const user = this.user;

      if (!user || !user.canViewWhoTypes()) {
        return;
      }

      items.add(
        'realtimeItems',
        <FieldSet className="Settings-realtime" label={app.translator.trans('blomstra-realtime.forum.user.settings.heading')}>
          {RealtimeUserPreferencesItems(user).toArray()}
        </FieldSet>,
        55
      );
    });
  }
}

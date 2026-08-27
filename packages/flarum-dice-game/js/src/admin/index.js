import {extend, override} from 'flarum/extend';
import SettingsPage from './components/SettingsPage';

app.initializers.add('ziven-dice-game', () => {
  app.extensionData
    .for('ziiven-dice-game').registerPage(SettingsPage)
    .registerPermission({
        icon: 'fas fa-dice',
        label: app.translator.trans('ziven-dice-game.admin.permission.allow-dice-game'),
        permission: 'ziven.zivenAllowDiceGame',
      },
      'moderate',
      90
    );
});

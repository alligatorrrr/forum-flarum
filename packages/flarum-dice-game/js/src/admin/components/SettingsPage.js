import ExtensionPage from 'flarum/components/ExtensionPage';

export default class SettingsPage extends ExtensionPage {
  oninit(attrs) {
    super.oninit(attrs);
    this.loading = false;
  }

  content() {
    return (
      <div className="ExtensionPage-settings FlarumBadgesPage">
        <div className="container">
          {this.buildSettingComponent({
            type: 'number',
            setting: 'ziven-dice-game.maxChallengeCount',
            label: app.translator.trans('ziven-dice-game.admin.settings.maxChallengeCount'),
            placeholder:'10',
            min:'1'
          })}
          {this.buildSettingComponent({
            type: 'number',
            setting: 'ziven-dice-game.minChallengeWager',
            label: app.translator.trans('ziven-dice-game.admin.settings.minChallengeWager'),
            placeholder:'10',
            min:'1'
          })}
          <div className="Form-group">{this.submitButton()}</div>
        </div>
      </div>
    );
  }
}

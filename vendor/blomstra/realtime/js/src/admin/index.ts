import app from 'flarum/admin/app';

app.initializers.add('blomstra-realtime', () => {
  app.extensionData
    .for('blomstra-realtime')
    .registerPermission(
      {
        icon: 'fas fa-keyboard',
        label: app.translator.trans('blomstra-realtime.admin.permission.view-who-types'),
        permission: 'discussion.blomstra-realtime.view-who-types',
        allowGuest: true,
      },
      'view'
    )
    .registerSetting({
      setting: 'blomstra-realtime.typing-indicator',
      label: app.translator.trans('blomstra-realtime.admin.settings.typing-indicator'),
      help: app.translator.trans('blomstra-realtime.admin.settings.typing-indicator-help'),
      type: 'boolean',
    })
    .registerSetting({
      setting: 'blomstra-realtime.release-discussion-updates',
      label: app.translator.trans('blomstra-realtime.admin.settings.release-discussion-updates'),
      help: app.translator.trans('blomstra-realtime.admin.settings.release-discussion-updates-help'),
      type: 'boolean',
    })
    .registerSetting({
      setting: 'blomstra-realtime.release-discussion-updates-interval',
      label: app.translator.trans('blomstra-realtime.admin.settings.release-discussion-updates-interval'),
      help: app.translator.trans('blomstra-realtime.admin.settings.release-discussion-updates-interval-help'),
      type: 'number',
    });
});

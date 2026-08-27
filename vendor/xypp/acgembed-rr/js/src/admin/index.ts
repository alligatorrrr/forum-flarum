import app from 'flarum/admin/app';

app.initializers.add('xypp/acgembed-rr', () => {
  app.extensionData
    .for('xypp-acgembed-rr')
    .registerSetting(
      {
        setting: 'xypp.acgembed-rr.tags', // This is the key the settings will be saved under in the settings table in the database.
        label: app.translator.trans('xypp-acgembed-rr.admin.settings.tags.label'), // The label to be shown letting the admin know what the setting does.
        help: app.translator.trans('xypp-acgembed-rr.admin.settings.tags.desc'), // Optional help text where a longer explanation of the setting can go.
        type: 'textarea', // What type of setting this is, valid options are: boolean, text (or any other <input> tag type), and select. 
      },
      30 // Optional: Priority
    )
    .registerSetting(
      {
        setting: 'xypp.acgembed-rr.lazy', // This is the key the settings will be saved under in the settings table in the database.
        label: app.translator.trans('xypp-acgembed-rr.admin.settings.lazy.label'), // The label to be shown letting the admin know what the setting does.
        help: app.translator.trans('xypp-acgembed-rr.admin.settings.lazy.desc'), // Optional help text where a longer explanation of the setting can go.
        type: 'boolean', // What type of setting this is, valid options are: boolean, text (or any other <input> tag type), and select. 
      },
      30 // Optional: Priority
    ).registerSetting(
      {
        setting: 'xypp.acgembed-rr.show-url', // This is the key the settings will be saved under in the settings table in the database.
        label: app.translator.trans('xypp-acgembed-rr.admin.settings.show-url.label'), // The label to be shown letting the admin know what the setting does.
        help: app.translator.trans('xypp-acgembed-rr.admin.settings.show-url.desc'), // Optional help text where a longer explanation of the setting can go.
        type: 'boolean', // What type of setting this is, valid options are: boolean, text (or any other <input> tag type), and select. 
      },
      30 // Optional: Priority
    ).registerSetting(
      {
        setting: 'xypp.acgembed-rr.fetch', // This is the key the settings will be saved under in the settings table in the database.
        label: app.translator.trans('xypp-acgembed-rr.admin.settings.fetch.label'), // The label to be shown letting the admin know what the setting does.
        help: app.translator.trans('xypp-acgembed-rr.admin.settings.fetch.desc'), // Optional help text where a longer explanation of the setting can go.
        type: 'boolean', // What type of setting this is, valid options are: boolean, text (or any other <input> tag type), and select. 
      },
      30 // Optional: Priority
    );
});

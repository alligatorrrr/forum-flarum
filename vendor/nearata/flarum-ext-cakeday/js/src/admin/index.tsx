import app from "flarum/admin/app";

const trans = (key: string) => {
  return app.translator.trans(`nearata-cakeday.admin.settings.${key}`);
};

app.initializers.add("nearata-cakeday", () => {
  app.extensionData
    .for("nearata-cakeday")
    .registerSetting(() => {
      return <h2>{trans("general_section_title")}</h2>;
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.cake_bg_color",
      type: "color-preview",
      label: trans("cake_bg_color_label"),
      help: trans("cake_bg_color_help"),
      placeholder: "#FFD449",
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.cake_text_color",
      type: "color-preview",
      label: trans("cake_txt_color_label"),
      help: trans("cake_txt_color_help"),
      placeholder: "#FFFFFF",
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.anniversaries_page",
      type: "boolean",
      label: trans("anniversaries_page_label"),
    })
    .registerSetting(() => {
      return <h2>{trans("members_section_title")}</h2>;
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.new_members",
      type: "boolean",
      label: trans("new_members"),
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.new_members_days",
      type: "number",
      label: trans("new_members_howlong"),
      help: trans("new_members_howlong_help"),
      autocomplete: "off",
      placeholder: "7",
      min: "1",
    })
    .registerSetting({
      setting: "nearata-cakeday.admin.new_members_label",
      type: "checkbox",
      label: trans("new_members_changelabel"),
    })
    .registerPermission(
      {
        icon: "fas fa-birthday-cake",
        label: app.translator.trans(
          "nearata-cakeday.admin.permissions.can_view_anniversaries_page"
        ),
        permission: "nearata-cakeday.can-view-anniversaries-page",
      },
      "view"
    );
});

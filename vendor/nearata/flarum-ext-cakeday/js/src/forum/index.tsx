import Badge from "flarum/common/components/Badge";
import LinkButton from "flarum/common/components/LinkButton";
import { extend } from "flarum/common/extend";
import User from "flarum/common/models/User";
import app from "flarum/forum/app";
import IndexPage from "flarum/forum/components/IndexPage";

const memberDay = (today, joinTime) => {
  const days = app.forum.attribute("cakedayNewMembersDays");
  const endDate = window.dayjs(joinTime).add(days, "day");

  return today < endDate;
};

app.initializers.add("nearata-cakeday", () => {
  extend(IndexPage.prototype, "navItems", (items) => {
    if (!app.session.user?.attribute<boolean>("canNearataCakedayViewPage")) {
      return;
    }

    items.add(
      "nearataCakedayAnniversaries",
      <LinkButton
        href={app.route("nearata-cakeday.anniversaries")}
        icon="fas fa-birthday-cake"
      >
        {app.translator.trans("nearata-cakeday.forum.page.title")}
      </LinkButton>
    );
  });

  extend(User.prototype, "badges", function (items) {
    const joinTime = this.joinTime();

    const join = window.dayjs(joinTime);
    const today = window.dayjs();

    let label = app.translator.trans("nearata-cakeday.forum.anniversary");

    const isMemberDay = app.forum.attribute("cakedayNewMembers");
    const isSameYear =
      today.year() === join.year() && memberDay(today, joinTime);

    if (isMemberDay && (isSameYear || memberDay(today, join))) {
      if (app.forum.attribute("cakedayNewMembersLabel")) {
        label = app.translator.trans("nearata-cakeday.forum.new_member_label");
      }
    } else {
      const isMonth = join.month() === today.month();
      const isDay = join.date() === today.date();

      if (!(isMonth && isDay)) {
        return;
      }
    }

    const bgColor = app.forum.attribute("cakedayBgColor");
    const textColor = app.forum.attribute("cakedayTextColor");

    items.add(
      "nearataCakeday",
      <Badge
        type="cakeday"
        icon="fas fa-birthday-cake"
        label={label}
        style={`background-color:${bgColor};color:${textColor}`}
      />
    );
  });
});

export { default as extend } from "./extend";

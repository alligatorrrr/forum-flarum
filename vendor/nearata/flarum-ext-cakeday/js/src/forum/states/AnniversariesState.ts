import User from "flarum/common/models/User";
import extractText from "flarum/common/utils/extractText";
import app from "flarum/forum/app";

const trans = (key: string) => {
  return app.translator.trans(
    `nearata-cakeday.forum.page.filter_options.${key}`
  );
};

const text1 = (dayjs: any) => {
  const date = dayjs.format("ll");

  return app.translator.trans(
    "nearata-cakeday.forum.page.anniversaries_for_label",
    { date }
  );
};

const text2 = (dayjs1: any, dayjs2: any) => {
  const date1 = dayjs1.format("ll");
  const date2 = dayjs2.format("ll");

  return app.translator.trans(
    "nearata-cakeday.forum.page.anniversaries_between_label",
    { date1, date2 }
  );
};

export default class AnniversariesState {
  users: User[];
  loading: boolean;
  hasMoreResults: boolean;
  currentFilter: string;
  filterOptions: { [key: string]: string };

  constructor() {
    this.users = [];
    this.loading = false;
    this.hasMoreResults = false;
    this.currentFilter = "today";
    this.filterOptions = {
      today: extractText(trans("today")),
      tomorrow: extractText(trans("tomorrow")),
      upcoming: extractText(trans("upcoming")),
      all: extractText(trans("all")),
    };
  }

  async loadMore() {
    this.loading = true;

    await app.store
      .find<User[]>("users", this.params())
      .then((r) => {
        this.users.push(...r);
        this.hasMoreResults = !!r.payload.links?.next;
      })
      .catch(() => {})
      .finally(() => {
        this.loading = false;
        m.redraw();
      });
  }

  params() {
    const params: { [key: string]: any } = {};

    params["page"] = { offset: this.users.length };
    params["sort"] = "joinedAt";
    params["filter"] = { cakeday: this.currentFilter };

    return params;
  }

  refresh(option: string = this.currentFilter) {
    this.currentFilter = option;

    this.users = [];

    this.loadMore();
  }

  getH2() {
    // @ts-ignore
    const today = window.dayjs();
    const tomorrow = today.add(1, "day");
    const upcoming = tomorrow.add(1, "day");

    let text = "";

    switch (this.currentFilter) {
      case "today":
        text = extractText(text1(today));
        break;
      case "tomorrow":
        text = extractText(text1(tomorrow));
        break;
      case "upcoming":
        const upcomingTo = upcoming.add(1, "week");
        text = extractText(text2(upcoming, upcomingTo));
        break;
      case "all":
        const start = today.startOf("year");
        const end = today.endOf("year");
        text = extractText(text2(start, end));
        break;
      default:
        break;
    }

    return text;
  }
}

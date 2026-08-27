import AnniversariesPage from "./components/AnniversariesPage";
import Extend from "flarum/common/extenders";

export default [
  new Extend.Routes().add(
    "nearata-cakeday.anniversaries",
    "/anniversaries",
    AnniversariesPage
  ),
];

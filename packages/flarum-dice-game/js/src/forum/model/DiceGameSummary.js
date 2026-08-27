import Model from "flarum/Model";

export default class DiceGameSummary extends Model {}
Object.assign(DiceGameSummary.prototype, {
  id: Model.attribute("id"),
  winCount: Model.attribute("winCount"),
  defeatCount: Model.attribute("defeatCount"),
});
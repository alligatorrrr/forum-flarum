import Model from "flarum/Model";

export default class DiceGameUser extends Model {}
Object.assign(DiceGameUser.prototype, {
  id: Model.attribute("id"),
  dice: Model.attribute("dice"),
  wager: Model.attribute("wager"),
  result: Model.attribute("result"),
  assignedAt: Model.attribute("assignedAt"),
  gameUserData: Model.hasOne("gameUserData"),
  diceGameData: Model.hasOne("diceGameData"),
  userData: Model.hasOne("userData"),
});
import Model from "flarum/Model";

export default class DiceGame extends Model {}
Object.assign(DiceGame.prototype, {
  id: Model.attribute("id"),
  dice: Model.attribute("dice"),
  defeatCount: Model.attribute("defeatCount"),
  challengeCount: Model.attribute("challengeCount"),
  gamePlayedID: Model.attribute("gamePlayedID"),
  wager: Model.attribute("wager"),
  balance: Model.attribute("balance"),
  assignedAt: Model.attribute("assignedAt"),
  hostUserData: Model.hasOne("hostUserData"),
  participateUsers: Model.hasMany("participateUsers"),
});
import Modal from 'flarum/common/components/Modal';
import Stream from 'flarum/utils/Stream';
import Button from 'flarum/components/Button';

import startDiceGameSuccessModal from "./startDiceGameSuccessModal";

export default class startDiceGameModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = false;
    this.minChallengeWager = app.forum.attribute("minChallengeWager");
    this.maxChallengeCount = app.forum.attribute("maxChallengeCount");
    this.hostWager = Stream(this.minChallengeWager);
    this.challengeQuantity = Stream(1);
    this.currentUser = app.session.user;
  }

  className() {
    return 'Modal--small';
  }

  onready(){
    const _this = this;
    $(".ZivenDiceGameInput_challengeQuantity").on("keyup",function(e){
      let value = parseInt((this.value).replace(/[^0-9]/g, ""));

      if(isNaN(value)){
        value = "";
      }

      $(this).val(value);
      _this.challengeQuantity(value);
      m.redraw();
    });
  }

  title() {
    return app.translator.trans('ziven-dice-game.forum.start-game');
  }

  content() {
    const userName = this.currentUser.displayName();
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const userMoneyText = moneyName.replace('[money]', this.currentUser.attribute("money"));
    const minChallengeWagerText = moneyName.replace('[money]', this.minChallengeWager);
    
    return [
      <div className="Modal-body" style="text-align: center;">
        <div className="Form-group ZivenDiceGameUserName">
          {userName}
        </div>
        <div className="Form-group">
          {app.translator.trans('ziven-dice-game.forum.start-game-info',{money:userMoneyText,wager:minChallengeWagerText})}
        </div>
        <div className="Form-group ZivenDiceGameInput">
          <div className="ZivenDiceGameInputLabel">
            {app.translator.trans('ziven-dice-game.forum.host-wager')}
          </div>
          <div>
            <input disabled={this.loading} className="FormControl" type="number" min={this.minChallengeWager} bidi={this.hostWager} />
          </div>
        </div>
        <div className="Form-group ZivenDiceGameInput">
          <div className="ZivenDiceGameInputLabel">
            {app.translator.trans('ziven-dice-game.forum.challenge-quantity')}
          </div>
          <div>
            <input disabled={this.loading} className="FormControl ZivenDiceGameInput_challengeQuantity" type="number" min="1" max={this.maxChallengeCount} bidi={this.challengeQuantity} />
          </div>
        </div>
        <div className="Form-group ZivenDiceGameDesc">
          {app.translator.trans('ziven-dice-game.forum.start-game-max-challenge',{quantity:this.maxChallengeCount})}
        </div>

        <div style="display: flex;justify-content: center;column-gap: 10px;">
          <Button disabled={this.loading || isNaN(this.challengeQuantity()) || (this.challengeQuantity()<1 || this.challengeQuantity()>this.maxChallengeCount)} className="Button Button--primary" style="min-width:60px" onclick={(e) => {
            this.confirm(e);
          }} >
            {app.translator.trans('ziven-dice-game.forum.confirm')}
          </Button>
          <Button disabled={this.loading} className="Button ZiveDiceGameButton--gray" style="min-width:60px" onclick={() => {
            this.hide();
          }}>
            {app.translator.trans('ziven-dice-game.forum.cancel')}
          </Button>
        </div>
      </div>
    ];
  }

  confirm(e){
    e.preventDefault();

    this.loading = true;

    const wager = this.hostWager();
    const challengeQuantity = this.challengeQuantity();
    const submitData = {
      wager:wager,
      quantity:challengeQuantity
    };

    app.store
    .createRecord("zivenDiceGame")
    .save(submitData)
    .then((result) => {
        $(window).trigger('zivenDiceGameRefresh');
        app.modal.show(startDiceGameSuccessModal);
        app.store.getById("users",this.currentUser.id()).data.attributes.money-=wager;
      }
    )
    .catch((e) => {
      this.loading = false;
    });
  }
}

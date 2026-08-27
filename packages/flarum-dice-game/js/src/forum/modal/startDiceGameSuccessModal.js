import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/components/Button';

export default class startDiceGameSuccessModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
  }

  className() {
    return 'Modal--small';
  }

  title() {
    return app.translator.trans('ziven-dice-game.forum.start-game');
  }

  content() {
    return [
      <div className="Modal-body" style="text-align: center;">
        <div className="Form-group ZivenDiceGameStartSuccess">
          {app.translator.trans('ziven-dice-game.forum.start-game-success')}
        </div>
        <div style="display: flex;justify-content: center;column-gap: 10px;">
          <Button className="Button ZiveDiceGameButton--gray" style="min-width:60px" onclick={() => {
            this.hide();
          }}>
            {app.translator.trans('ziven-dice-game.forum.close')}
          </Button>
        </div>
      </div>
    ];
  }
}

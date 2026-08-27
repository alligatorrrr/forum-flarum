import app from 'flarum/forum/app';
import { storeBox } from './storeBox';
import DiscussionControls from 'flarum/forum/utils/DiscussionControls';
import { extend } from 'flarum/common/extend';
import Button from 'flarum/common/components/Button';
import Modal from 'flarum/common/components/Modal';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Alert from 'flarum/common/components/Alert';
import setRouteWithForcedRefresh from "flarum/common/utils/setRouteWithForcedRefresh";
import DiscussionStickyCardUsage from './components/DiscussionStickyCardUsage';
class loadingModal extends Modal {
  className(): string { return 'Modal'; }
  title() {
    return app.translator.trans('xypp-sticky-card.forum.loading');
  }
  content() { return LoadingIndicator.component() }
}
app.initializers.add('xypp/sticky-card', () => {
  3
  app.postComponents.discussionStickyCardUsage = DiscussionStickyCardUsage;
  if ('xypp-store' in flarum.extensions) {
    storeBox(app);
  }
  extend(DiscussionControls, 'moderationControls', function (items, discussion) {
    items.add('sticky-card', Button.component({
      icon: 'fas fa-hand-point-up',
      onclick: async function () {
        app.modal.show(loadingModal);
        let helper = await (flarum.extensions['xypp-store'].UseHelper as any).get("sticky-card");
        await helper.filterAvailable().filterWithData((data: any) => (!data.data)).expireTime().query();
        if (!helper.hasItem()) {
          const buy = await (flarum.extensions['xypp-store'].PurchaseHelper as any).get("sticky-card");
          await buy.query();
          if (buy.hasItem()) {
            await buy.purchase();
            helper = await (flarum.extensions['xypp-store'].UseHelper as any).get("sticky-card");
            await helper.filterAvailable().filterWithData((data: any) => (!data.data)).expireTime();
          }
        }
        if (!helper.hasItem()) {
          app.alerts.show(Alert, { type: "warn" }, app.translator.trans('xypp-sticky-card.forum.no-card'));
          app.modal.close();
          return;
        }
        await helper.use(discussion.id())
        app.modal.close();
        app.alerts.show(Alert, { type: "success" }, app.translator.trans('xypp-sticky-card.forum.success'));
        setRouteWithForcedRefresh(app.history.getCurrent().url);

      }
    }, app.translator.trans('xypp-sticky-card.forum.use-sticky-card')));
  });
});

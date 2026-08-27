import { extend, override } from 'flarum/common/extend';
import ForumApplication from 'flarum/forum/ForumApplication';
import Store from './StoreHelper';
export function storeBox(app: ForumApplication) {
  Store.addFrontendProviders(
    "sticky-card", app.translator.trans("xypp-sticky-card.forum.sticky-card") as string,
    undefined,
    function createItemShowCase(item, purchase_history) {
      return (
        <div style="text-align:center;">
          <br></br>
          <p><i class="fas fa-hand-point-up"></i></p>
          <p>{purchase_history?.attribute("data")?.usedOn ?
            app.translator.trans("xypp-sticky-card.forum.used", [purchase_history?.attribute("data")?.usedOn] as any) :
            app.translator.trans("xypp-sticky-card.forum.not-used")}</p>
        </div>
      );
    },
    async function (str): Promise<string> {
      return "";
    }
  )
}

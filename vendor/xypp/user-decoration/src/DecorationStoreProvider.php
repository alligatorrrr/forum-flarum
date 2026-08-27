<?php

namespace Xypp\UserDecoration;

use Flarum\User\User;
use Xypp\Store\AbstractStoreProvider;
use Xypp\Store\PurchaseHistory;
use Xypp\Store\StoreItem;
use Xypp\UserDecoration\Utils\UserOwnDecorationUtil;
use Xypp\Store\Context\PurchaseContext;
use Xypp\Store\Context\UseContext;
use Xypp\Store\Context\ExpireContext;
class DecorationStoreProvider extends AbstractStoreProvider
{
    public $name = "decoration";
    public $canSeeInHistory = true;
    public $canUse = true;
    public $singleHold = true;
    public $canUseFrontend = true;
    public function useItem(PurchaseHistory $item, User $user, string $data, UseContext $context): bool
    {
        try {
            $decoration = UserDecoration::findOrFail($item->store_item()->first()->provider_data);
            $user->setAttribute($decoration->type . "_decoration", $decoration->id);
            $user->save();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public function purchase(StoreItem $item, User $user, PurchaseHistory|null $old = null, PurchaseContext $context): array|bool|string
    {
        if ($old) {
            if (UserOwnDecoration::find($old->data)->exists())
                return $old->data;
        }
        UserOwnDecorationUtil::AssertAddUserDecorationId($user->id, $item->provider_data);
        $newModel = new UserOwnDecoration();
        $newModel->user_id = $user->id;
        $newModel->decoration_id = $item->provider_data;
        $decoration = UserDecoration::findOrFail($newModel->decoration_id);
        $newModel->type = $decoration->type;
        $newModel->save();
        return $newModel->id;
    }
    public function serialize(StoreItem $item, PurchaseHistory $historyItem = null): array
    {
        $decoration = UserDecoration::findOrFail($item->provider_data);
        return [
            "name" => $decoration->name,
            "desc" => $decoration->desc,
            "type" => $decoration->type,
            "id" => $decoration->id
        ];
    }
    public function canPurchase(StoreItem $item, User $user): bool|string
    {
        if ($item->expire_time) {
            return true;
        }
        if (
            UserOwnDecoration::where([
                'user_id' => $user->id,
                'decoration_id' => $item->provider_data
            ])->exists()
        ) {
            return "decoration.dumplicate";
        }
        return true;
    }
    public function expire(PurchaseHistory $item,ExpireContext $context): bool
    {
        $id = $item->data;
        $model = UserOwnDecoration::find($id);
        if (!$model)
            return false;

        $user = User::findOrFail($model->user_id);
        if ($user->avatar_decoration == $model->decoration_id)
            $user->avatar_decoration = null;
        if ($user->name_decoration == $model->decoration_id)
            $user->name_decoration = null;
        if ($user->card_decoration == $model->decoration_id)
            $user->card_decoration = null;
        if ($user->post_decoration == $model->decoration_id)
            $user->post_decoration = null;
        if ($user->isDirty())
            $user->save();
        $model->delete();
        return true;
    }
}

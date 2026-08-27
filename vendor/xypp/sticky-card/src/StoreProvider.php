<?php
/*
 * This file is part of xypp/sticky-card.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
namespace Xypp\StickyCard;

use Flarum\Discussion\Discussion;
use Flarum\User\User;
use Xypp\Store\AbstractStoreProvider;
use Xypp\Store\Context\PurchaseContext;
use Xypp\Store\Context\UseContext;
use Xypp\Store\PurchaseHistory;
use Xypp\Store\StoreItem;
use Carbon\Carbon;
use Xypp\Store\Context\ExpireContext;

class StoreProvider extends AbstractStoreProvider
{
    public $name = "sticky-card";
    public $canSeeInHistory = true;
    public $canUse = true;
    public $canUseFrontend = false;

    public function expire(PurchaseHistory $item, ExpireContext $context): bool
    {
        if ($item->data != "default") {
            $d = Discussion::find($item->data);
            if ($d) {
                if (!$d->getAttribute("is_sticky"))
                    return true;

                $d->setAttribute("is_sticky", false);
                if ($d->save()) {
                    $d->mergePost(PostStickCardEffect::reply($d->id, $item->user->id, false));
                }
            }
        }
        return true;
    }
    public function purchase(StoreItem $item, User $user, PurchaseHistory|null $old = null, PurchaseContext $context): array|bool|string
    {
        $context->setExpire(null);
        return "default";
    }

    public function useItem(PurchaseHistory $item, User $user, string $data, UseContext $context): bool
    {
        if ($item->data != "default") {
            $d = Discussion::find($item->data);
            if ($d) {
                if ($d->getAttribute("is_sticky")) {
                    $d->setAttribute("is_sticky", false);
                    $d->save();
                    $d->mergePost(PostStickCardEffect::reply($d->id, $item->user->id, false));
                }
            }
        }
        if ($item->expire_at === null) {
            $store = $context->getStoreItem();
            if ($store->expire_time !== null) {
                $context->setExpire(Carbon::now()->addSeconds($store->expire_time));
            }
        }
        $item->data = $data;
        $d = Discussion::find($item->data);
        if (!$d)
            return false;
        if ($d->getAttribute("is_sticky")) {
            $context->exceptionWith("xypp-sticky-card.api.already_sticky");
        }
        $d->setAttribute("is_sticky", true);
        if ($d->save()) {
            $d->mergePost(PostStickCardEffect::reply($d->id, $user->id, true));
            return true;
        } else {
            return false;
        }
    }

    public function canPurchase(StoreItem $item, User $user): bool|string
    {
        return true;
    }
    public function serializeHistory(PurchaseHistory $item): array
    {
        $ptitle = "";
        if ($item->data != "default") {
            $d = Discussion::find($item->data);
            if ($d) {
                $ptitle = $d->title;
            }
        }
        return [
            "data" => $item->data == "default" ? "" : $item->data,
            "usedOn" => $ptitle
        ];
    }
}
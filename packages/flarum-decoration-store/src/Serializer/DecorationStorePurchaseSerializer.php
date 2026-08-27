<?php
namespace Ziven\decorationStore\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Ziven\decorationStore\Serializer\DecorationStoreSerializer;

class DecorationStorePurchaseSerializer extends AbstractSerializer{
    protected $type = 'decorationStorePurchase';

    protected function getDefaultAttributes($purchase){
        return [
            'id' => $purchase->id,
            'item_id' => $purchase->item_id,
            'user_id' => $purchase->user_id,
            'item_count' => $purchase->item_count,
            'item_type' => $purchase->item_type,
            'purchase_type' => $purchase->purchase_type,
            'purchase_cost' => $purchase->purchase_cost,
            'purchase_discount' => $purchase->purchase_discount,
            'item_status' => $purchase->item_status,
            'assigned_at' => $purchase->assigned_at,
            'expired_days' => $purchase->expired_days,
            'is_expired' => $purchase->is_expired,
        ];
    }

    protected function decorationData($data){
        return $this->hasOne($data, DecorationStoreSerializer::class);
    }
}

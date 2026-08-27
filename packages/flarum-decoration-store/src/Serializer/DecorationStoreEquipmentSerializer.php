<?php
namespace Ziven\decorationStore\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Ziven\decorationStore\Serializer\DecorationStoreSerializer;

class DecorationStoreEquipmentSerializer extends AbstractSerializer{
    protected $type = 'decorationStoreEquipment';

    protected function getDefaultAttributes($purchase){
        return [
            'id' => $purchase->id,
            'item_id' => $purchase->item_id,
            'user_id' => $purchase->user_id,
            'item_count' => $purchase->item_count,
            'item_type' => $purchase->item_type,
            'purchase_type' => $purchase->purchase_type,
            'purchase_cost' => $purchase->purchase_cost,
            'item_status' => $purchase->item_status,
            'assigned_at' => $purchase->assigned_at,
            'expired_at' => $purchase->expired_at,
            'is_expired' => $purchase->is_expired,
        ];
    }

    protected function decorationData($data){
        return $this->hasOne($data, DecorationStoreSerializer::class);
    }
}

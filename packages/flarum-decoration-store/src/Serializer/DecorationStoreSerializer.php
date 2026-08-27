<?php
namespace Ziven\decorationStore\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class DecorationStoreSerializer extends AbstractSerializer{
    protected $type = 'decorationStoreList';

    protected function getDefaultAttributes($shop){
        return [
            'id' => $shop->id,
            'purchase_id' => $shop->purchase_id,
            'item_title' => $shop->item_title,
            'item_desc' => $shop->item_desc,
            'item_type' => $shop->item_type,
            'item_cost' => $shop->item_cost,
            'item_sold' => $shop->item_sold,
            'item_label_recommend' => $shop->item_label_recommend,
            'item_label_popular' => $shop->item_label_popular,
            'item_amount' => $shop->item_amount,
            'item_discount' => $shop->item_discount,
            'item_discount_days' => $shop->item_discount_days,
            'item_discount_date' => $shop->item_discount_date,
            'item_property' => $shop->item_property,
            'purchase_type' => $shop->purchase_type,
            'related_id' => $shop->related_id,
            'assigned_at' => $shop->assigned_at,
            'sort' => $shop->sort,
            'isActivate' => $shop->isActivate,
        ];
    }
}

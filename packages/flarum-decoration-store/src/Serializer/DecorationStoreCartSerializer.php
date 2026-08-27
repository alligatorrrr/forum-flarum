<?php
namespace Ziven\decorationStore\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class DecorationStoreCartSerializer extends AbstractSerializer{
    protected $type = 'decorationCartList';

    protected function getDefaultAttributes($cart){
        return [
            'id' => $cart->id,
            'item_id' => $cart->item_id,
            'user_id' => $cart->user_id,
            'item_count' => $cart->item_count,
            'is_valid' => $cart->is_valid,
            'assigned_at' => $cart->assigned_at,
        ];
    }
}

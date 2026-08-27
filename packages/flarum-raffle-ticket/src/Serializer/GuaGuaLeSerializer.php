<?php

namespace Ziven\GuaGuaLe\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Ziven\GuaGuaLe\Serializer\GuaGuaLePurchaseCountSerializer;

class GuaGuaLeSerializer extends AbstractSerializer{
    protected $type = 'guagualeList';

    protected function getDefaultAttributes($data){
        $attributes = [
            'id' => $data->id,
            'title' => $data->title,
            'desc' => $data->desc,
            'color' => $data->color,
            'image' => $data->image,
            'amount' => $data->amount,
            'purchased' => $data->purchased,
            'settings' => $data->settings,
            'cost' => $data->cost,
            'limit' => $data->limit,
            'activated' => $data->activated,
            'assigned_at' => date("Y-m-d H:i:s", strtotime($data->assigned_at))
        ];

        return $attributes;
    }
}

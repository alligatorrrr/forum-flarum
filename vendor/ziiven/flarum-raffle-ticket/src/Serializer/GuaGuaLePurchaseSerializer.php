<?php

namespace Ziven\GuaGuaLe\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Ziven\GuaGuaLe\Serializer\GuaGuaLeSerializer;

class GuaGuaLePurchaseSerializer extends AbstractSerializer{
    protected $type = 'guagualePurchase';

    protected function getDefaultAttributes($data){
        $attributes = [
            'id' => $data->id,
            'title'=> $data->title,
            'gua_id' => $data->gua_id,
            'user_id' => $data->user_id,
            'pruchase_count' => $data->pruchase_count,
            'pruchase_cost' => $data->pruchase_cost,
            'pruchase_cost_total' => $data->pruchase_cost_total,
            'pruchase_win_total' => $data->pruchase_win_total,
            'pruchase_result' => $data->pruchase_result,
            'opened' => $data->opened,
            'assigned_at' => date("Y-m-d H:i:s", strtotime($data->assigned_at)),
            'open_at' => date("Y-m-d H:i:s", strtotime($data->open_at))
        ];

        return $attributes;
    }

    protected function guagualeData($data){
        return $this->hasOne($data, GuaGuaLeSerializer::class);
    }

    protected function purchasedUser($guagualePurchase){
        return $this->hasOne($guagualePurchase, BasicUserSerializer::class);
    }
}

<?php

namespace Ziven\GuaGuaLe\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class GuaGuaLePurchaseCountSerializer extends AbstractSerializer{
    protected $type = 'guagualePurchaseCount';

    protected function getDefaultAttributes($data){
        $attributes = [
            'gua_id'=>$data->id,
            'total_pruchase_count' => $data->total_pruchase_count
        ];

        return $attributes;
    }
}

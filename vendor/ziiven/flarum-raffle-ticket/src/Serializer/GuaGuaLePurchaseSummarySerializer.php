<?php

namespace Ziven\GuaGuaLe\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Ziven\GuaGuaLe\Serializer\GuaGuaLeSerializer;

class GuaGuaLePurchaseSummarySerializer extends AbstractSerializer{
    protected $type = 'guagualePurchaseHistorySummary';

    protected function getDefaultAttributes($data){
        $attributes = [
            'costTotal' => $data["costTotal"],
            'winTotal' => $data["winTotal"],
        ];

        return $attributes;
    }
}

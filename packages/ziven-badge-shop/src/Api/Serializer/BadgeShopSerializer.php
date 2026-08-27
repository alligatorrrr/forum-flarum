<?php

namespace Ziven\BadgeShop\Api\Serializer;

use Ziven\BadgeShop\Model\BadgeShop;
use Flarum\Api\Serializer\AbstractSerializer;

class BadgeShopSerializer extends AbstractSerializer
{
    protected $type = 'badgeShopPurchase';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($data){
        $attributes = [
            'id' => $data->id,
            'badgeId' => $data->badge_id,
            'userId' => $data->user_id,
            'type' => $data->type,
            'cost' => $data->cost,
            'expired' => $data->is_expired,
            'purchaseAt' => date("Y-m-d H:i:s", strtotime($data->assigned_at))
        ];

        return $attributes;
    }
}

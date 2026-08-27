<?php

namespace Ziven\GuaGuaLe\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class GuaGuaLeTicketsSerializer extends AbstractSerializer{
    protected $type = 'guagualeTickets';

    protected function getDefaultAttributes($data){
        $attributes = [
            'id' => $data->id,
            'gua_id' => $data->gua_id,
            'value' => $data->value,
            'flag' => $data->flag
        ];

        return $attributes;
    }
}

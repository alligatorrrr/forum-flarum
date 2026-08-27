<?php

namespace Nearata\CakeDay\Api\Serializer;

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;
use Nearata\CakeDay\Helpers;

class BasicUserSerializerAttributes
{
    public function __invoke(BasicUserSerializer $serializer, User $user, array $attributes)
    {
        return [
            'canNearataCakedayViewPage' => Helpers::canView($user),
        ];
    }
}

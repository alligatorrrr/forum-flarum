<?php

namespace Nearata\CakeDay\Api\Serializer;

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;

class ExtendBasicUserSerializer
{
    public function __invoke(BasicUserSerializer $serializer, User $user, array $attributes)
    {
        return [
            'canNearataCakedayViewPage' => (bool) $user->can('nearata-cakeday.can_view_anniversaries_page')
        ];
    }
}

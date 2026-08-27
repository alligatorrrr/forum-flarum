<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Api\Serializer;


class BasicUserAttributeSerializer
{

    public function __invoke($serializer, $user, $attributes)
    {
        $avatar_decoration = "";
        if (isset($user->avatar_decoration)) {
            $avatar_decoration = $user->avatar_decoration;
        }
        $attributes['avatar_decoration'] = $avatar_decoration;

        $name_decoration = "";
        if (isset($user->name_decoration)) {
            $name_decoration = $user->name_decoration;
        }
        $attributes['name_decoration'] = $name_decoration;

        $card_decoration = "";
        if (isset($user->card_decoration)) {
            $card_decoration = $user->card_decoration;
        }
        $attributes['card_decoration'] = $card_decoration;

        $post_decoration = "";
        if (isset($user->post_decoration)) {
            $post_decoration = $user->post_decoration;
        }
        $attributes['post_decoration'] = $post_decoration;
        return $attributes;
    }
}
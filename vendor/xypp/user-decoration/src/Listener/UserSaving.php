<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Listener;

use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;

class UserSaving
{
    public function handle(Saving $event)
    {
        $data = $event->data;
        $user = $event->user;

        $attributes = Arr::get($data, 'attributes', []);
        if (isset($attributes["avatar_decoration"])) {
            if ($attributes["avatar_decoration"] == "null")
                $user->avatar_decoration = null;
            else
                $user->avatar_decoration = $attributes["avatar_decoration"];
        }
        if (isset($attributes["name_decoration"])) {
            if ($attributes["name_decoration"] == "null")
                $user->name_decoration = null;
            else
                $user->name_decoration = $attributes["name_decoration"];
        }
        if (isset($attributes["card_decoration"])) {
            if ($attributes["card_decoration"] == "null")
                $user->card_decoration = null;
            else
                $user->card_decoration = $attributes["card_decoration"];
        }
        if (isset($attributes["post_decoration"])) {
            if ($attributes["post_decoration"] == "null")
                $user->post_decoration = null;
            else
                $user->post_decoration = $attributes["post_decoration"];
        }
    }
}

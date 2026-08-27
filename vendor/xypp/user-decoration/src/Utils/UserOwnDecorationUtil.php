<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace Xypp\UserDecoration\Utils;

use Xypp\UserDecoration\UserOwnDecoration;

class UserOwnDecorationUtil
{
    public static function AssertAddUserDecorationId($user_id, $decoration_id)
    {
        if (
            UserOwnDecoration::where([
                'user_id' => $user_id,
                'decoration_id' => $decoration_id
            ])->exists()
        ) {
            throw new \RuntimeException("User already has this decoration", 501);
        }
    }
}
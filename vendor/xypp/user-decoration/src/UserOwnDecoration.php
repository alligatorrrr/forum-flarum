<?php

/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Xypp\UserDecoration;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class UserOwnDecoration extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'user_own_decoration';

    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function decoration()
    {
        return $this->hasOne(UserDecoration::class, "decoration_id");
    }
}

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

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;
use Xypp\UserDecoration\UserDecoration;

class UserDecorationRepository
{
    public function findInIds(array $ids)
    {
        $qb = UserDecoration::query();
        $qb->whereIn("id", $ids);
        return $qb->get();
    }
}

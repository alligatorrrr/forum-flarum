<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        $schema->table('user_decoration', function (Blueprint $table) {
            $table->string('name');
            $table->string('desc');
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('user_decoration', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('desc');
        });
    }
];
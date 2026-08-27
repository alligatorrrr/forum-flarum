<?php
/*
 * This file is part of xypp/user-decoration.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


use Flarum\Database\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable(
    'user_own_decoration',
    function (Blueprint $table) {
        $table->increments('id');
        $table->string('type', 20);
        $table->integer('user_id')->unsigned();
        $table->integer('decoration_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('decoration_id')->references('id')->on('user_decoration')->onDelete('cascade');
    }
);
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_dice_game_user')) {
            $schema->create('ziven_dice_game_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('game_id')->unsigned();
                $table->integer('user_id')->nullable()->unsigned();
                $table->integer('dice')->unsigned();
                $table->float('wager')->unsigned();
                $table->integer('result')->unsigned();
                $table->dateTime('assigned_at');
                
                $table->index('user_id');
                $table->index('game_id');
                $table->index('result');
                $table->index('assigned_at');
                $table->foreign('game_id')->references('id')->on('ziven_dice_game')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_dice_game_user');
    },
];

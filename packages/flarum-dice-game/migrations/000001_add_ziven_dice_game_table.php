<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_dice_game')) {
            $schema->create('ziven_dice_game', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable()->unsigned();
                $table->integer('dice')->unsigned();
                $table->integer('win_count')->default(0)->unsigned();
                $table->integer('defeat_count')->default(0)->unsigned();
                $table->integer('draw_count')->default(0)->unsigned();
                $table->integer('challenge_count')->default(0)->unsigned();
                $table->float('wager')->unsigned();
                $table->float('balance')->default(0);
                $table->integer('status')->default(0)->unsigned();
                $table->dateTime('assigned_at');
                
                $table->index('user_id');
                $table->index('assigned_at');
                $table->index('status');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_dice_game');
    },
];

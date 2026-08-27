<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_badge_shop_user')) {
            $schema->create('ziven_badge_shop_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('badge_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->string('type', 20);
                $table->integer('cost')->unsigned();;
                $table->dateTime('assigned_at');
                $table->boolean('is_expired')->default(0);

                $table->index('badge_id');
                $table->index('user_id');
                $table->index('is_expired');
                $table->unique(['badge_id', 'user_id']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_badge_shop_user');
    },
];

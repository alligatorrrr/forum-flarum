<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_decoration_store_cart')) {
            $schema->create('ziven_decoration_store_cart', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('item_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->integer('item_count')->unsigned();
                $table->boolean('is_valid')->default(1);
                $table->dateTime('assigned_at');

                $table->foreign('item_id')->references('id')->on('ziven_decoration_store')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->index('assigned_at');
                $table->index('is_valid');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_decoration_store_cart');
    },
];

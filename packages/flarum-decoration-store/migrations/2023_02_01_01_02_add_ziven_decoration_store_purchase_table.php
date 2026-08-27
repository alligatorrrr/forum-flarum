<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_decoration_store_purchase')) {
            $schema->create('ziven_decoration_store_purchase', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('item_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->integer('item_count')->unsigned();
                $table->string('item_type', 30);
                $table->string('purchase_type', 20);
                $table->float('purchase_cost')->unsigned();
                $table->integer('purchase_discount')->unsigned()->default(0);
                $table->integer('item_status')->unsigned()->default(0);
                $table->dateTime('assigned_at');
                $table->integer('expired_days')->unsigned();
                $table->boolean('is_expired')->default(0);

                $table->foreign('item_id')->references('id')->on('ziven_decoration_store')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->index('item_type');
                $table->index('purchase_type');
                $table->index('purchase_cost');
                $table->index('assigned_at');
                $table->index('item_status');
                $table->index('expired_days');
                $table->index('is_expired');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_decoration_store_purchase');
    },
];

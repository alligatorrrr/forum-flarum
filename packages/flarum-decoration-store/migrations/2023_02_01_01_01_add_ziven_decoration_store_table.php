<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_decoration_store')) {
            $schema->create('ziven_decoration_store', function (Blueprint $table) {
                $table->increments('id');
                $table->string('item_title', 255);
                $table->string('item_desc', 500);
                $table->string('item_type', 30);
                $table->integer('item_amount')->unsigned()->default(0);
                $table->integer('item_sold')->unsigned()->default(0);
                $table->float('item_cost')->unsigned()->default(0);
                $table->float('item_discount')->unsigned()->default(0);
                $table->dateTime('item_discount_date')->nullable();
                $table->integer('item_discount_days')->unsigned()->default(0);
                $table->string('item_property', 500);
                $table->boolean('item_label_recommend')->default(0);
                $table->boolean('item_label_popular')->default(0);
                $table->string('purchase_type', 20);
                $table->integer('related_id')->unsigned()->default(0);
                $table->dateTime('assigned_at');
                $table->integer('sort')->unsigned()->default(0);
                $table->boolean('isActivate')->default(0);

                $table->index('item_type');
                $table->index('item_cost');
                $table->index('item_discount');
                $table->index('item_discount_days');
                $table->index('item_label_recommend');
                $table->index('item_label_popular');
                $table->index('purchase_type');
                $table->index('related_id');
                $table->index('assigned_at');
                $table->index('sort');
                $table->index('isActivate');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_decoration_store');
    },
];

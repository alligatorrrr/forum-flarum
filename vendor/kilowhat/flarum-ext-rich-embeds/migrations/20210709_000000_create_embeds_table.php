<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('kilowhat_rich_embeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();
            $table->unsignedTinyInteger('http_status')->nullable();
            $table->string('error')->nullable();
            $table->json('opengraph')->nullable();
            $table->json('icons')->nullable();
            $table->json('fallback')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('retrieved_at')->nullable();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('kilowhat_rich_embeds');
    },
];

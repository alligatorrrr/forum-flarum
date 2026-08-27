<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('kilowhat_rich_embed_post', function (Blueprint $table) {
            $table->unsignedInteger('embed_id');
            $table->unsignedInteger('post_id');

            $table->primary(['embed_id', 'post_id']);

            $table->foreign('embed_id')->references('id')->on('kilowhat_rich_embeds')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('kilowhat_rich_embed_post');
    },
];

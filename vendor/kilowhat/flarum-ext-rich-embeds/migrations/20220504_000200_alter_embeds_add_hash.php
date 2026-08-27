<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Kilowhat\RichEmbeds\Embed;

return [
    'up' => function (Builder $schema) {
        $connection = $schema->getConnection();

        // Laravel doesn't have BINARY column definition ($table->binary is BLOB) so we need to use a raw statement
        // A hash is necessary because we want to handle URLs longer than 255 characters and we would no longer be able to have an index on the column
        $connection->statement("ALTER TABLE `{$connection->getTablePrefix()}kilowhat_rich_embeds` ADD url_hash binary(20) AFTER url");

        // Update existing entries
        Embed::query()->orderBy('id')->each(function ($embed) {
            $embed->url_hash = Embed::hashUrl($embed->url);
            $embed->save();
        });

        $schema->table('kilowhat_rich_embeds', function (Blueprint $table) {
            $table->unique('url_hash');
        });
    },
    'down' => function (Builder $schema) {
        if (!$schema->hasColumn('kilowhat_rich_embeds', 'url_hash')) {
            return;
        }

        $schema->table('kilowhat_rich_embeds', function (Blueprint $table) {
            $table->dropColumn('url_hash');
        });
    },
];

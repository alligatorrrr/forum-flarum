<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Kilowhat\RichEmbeds\Embed;

return [
    'up' => function (Builder $schema) {
        $connection = $schema->getConnection();
        $indexName = $connection->getTablePrefix() . 'kilowhat_rich_embeds_url_unique';
        $indexes = $connection->getDoctrineSchemaManager()->listTableIndexes('kilowhat_rich_embeds');

        // Check index exists before deleting
        // It seems like some clients had errors in the second part of the migration so this will allow replaying the migration
        if (array_key_exists($indexName, $indexes)) {
            $schema->table('kilowhat_rich_embeds', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }

        // Must use two table() clauses because index dropping always happens last in the clause
        // and we need it to be done before resizing the column
        $schema->table('kilowhat_rich_embeds', function (Blueprint $table) {
            $table->string('url', Embed::$urlStringLength)->change();
        });
    },
    'down' => function (Builder $schema) {
        // No need to revert this
        // Technically we should revert the UNIQUE index, but this would require deleting any incompatible value added since then
    },
];

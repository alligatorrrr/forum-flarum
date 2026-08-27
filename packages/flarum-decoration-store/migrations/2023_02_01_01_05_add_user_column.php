<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('users', 'decoration_avatarFrame')) {
            $schema->table('users', function (Blueprint $table) {
                $table->string('decoration_avatarFrame', 300)->nullable();
            });
            $schema->table('users', function (Blueprint $table) {
                $table->string('decoration_profileBackground', 300)->nullable();
            });
            $schema->table('users', function (Blueprint $table) {
                $table->string('decoration_usernameColor', 300)->nullable();
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('decoration_avatarFrame');
        });
        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('decoration_profileBackground');
        });
        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('decoration_usernameColor');
        });
    }
];

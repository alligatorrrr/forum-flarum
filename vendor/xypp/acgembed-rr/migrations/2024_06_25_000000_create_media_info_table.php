<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'media_info',
    function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->string('source');
        $table->string('url');
        $table->integer("post_id")->unsigned();
        $table->foreign("post_id")->on("posts")->references("id")->onDelete("cascade");
        $table->string("poster",1024);
        $table->string("title");
        $table->string("desc",1024);
    }
);


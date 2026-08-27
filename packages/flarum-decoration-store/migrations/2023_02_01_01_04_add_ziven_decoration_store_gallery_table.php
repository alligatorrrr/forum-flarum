<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Ziven\decorationStore\Model\DecorationStoreGallery;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('ziven_decoration_store_gallery')) {
            $schema->create('ziven_decoration_store_gallery', function (Blueprint $table) {
                $table->increments('id');
                $table->string('url', 50);
                $table->string('item_type', 30);
                $table->integer('count')->unsigned()->default(0);
                
                $table->index('count');
                $table->unique(['url']);
            });
            
            $data =  array();

            for($i=10000;$i<=10070;$i++){
                array_push($data, ['item_type'=>'avatarFrame','url' => $i.'.png']);
            }

            for($i=20000;$i<=20078;$i++){
                array_push($data, ['item_type'=>'profileBackground','url' => $i.'.jpg']);
            }

            for($i=1;$i<=13;$i++){
                array_push($data, ['item_type'=>'usernameColor','url' => 'decorationStoreColorText'.$i]);
            }

            foreach ($data as $datum){
                $gallery = new DecorationStoreGallery();
                $gallery->item_type = $datum['item_type'];
                $gallery->url = $datum['url'];
                $gallery->save();
            }
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('ziven_decoration_store_gallery');
    },
];

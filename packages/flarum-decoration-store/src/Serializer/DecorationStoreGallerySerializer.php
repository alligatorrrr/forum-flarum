<?php
namespace Ziven\decorationStore\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class DecorationStoreGallerySerializer extends AbstractSerializer{
    protected $type = 'decorationStoreGallery';

    protected function getDefaultAttributes($gallery){
        return [
            'id' => $gallery->id,
            'url' => $gallery->url,
            'item_type' => $gallery->item_type,
            'count' => $gallery->count,
        ];
    }
}

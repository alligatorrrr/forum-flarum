<?php

use Flarum\Database\Migration;
use Kilowhat\RichEmbeds\Embed;

return Migration::addColumns('kilowhat_rich_embeds', [
    'final_url' => ['string', 'length' => Embed::$urlStringLength, 'nullable' => true],
    'mime' => ['string', 'length' => Embed::$mimeStringLength, 'nullable' => true],
    'exif' => ['json', 'nullable' => true],
    'width' => ['integer', 'unsigned' => true, 'nullable' => true],
    'height' => ['integer', 'unsigned' => true, 'nullable' => true],
    'size' => ['bigInteger', 'unsigned' => true, 'nullable' => true],
]);

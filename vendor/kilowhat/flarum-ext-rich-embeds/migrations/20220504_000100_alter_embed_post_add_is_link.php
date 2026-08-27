<?php

use Flarum\Database\Migration;

return Migration::addColumns('kilowhat_rich_embed_post', [
    'is_link' => ['boolean', 'default' => true],
]);

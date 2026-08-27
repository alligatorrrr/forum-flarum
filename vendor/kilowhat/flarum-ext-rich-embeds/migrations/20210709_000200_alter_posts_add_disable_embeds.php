<?php

use Flarum\Database\Migration;

return Migration::addColumns('posts', [
    'kilowhat_rich_embeds_disable' => ['boolean', 'default' => false],
]);

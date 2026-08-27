<?php

use Flarum\Database\Migration;

return Migration::addColumns('kilowhat_rich_embeds', [
    'api_resource' => ['json', 'nullable' => true],
]);

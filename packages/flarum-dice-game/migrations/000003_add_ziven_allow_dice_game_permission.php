<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'ziven.zivenAllowDiceGame' => Group::MODERATOR_ID
]);

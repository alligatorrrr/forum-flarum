<?php

namespace Annonny\GptBot;

use Flarum\Extend;
use Flarum\Post\Event\Posted;

$extend = [
    (new Extend\Event())
        ->listen(Posted::class, [Listeners\GiveAnswer::class, 'postWasPosted'])

];

return $extend;

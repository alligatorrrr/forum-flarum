<?php

/*
 * This file is part of xypp/sticky-card.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Xypp\StickyCard;

use Flarum\Extend;

$ret = [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\Post())
        ->type(PostStickCardEffect::class)
];

if (class_exists(\Xypp\Store\AbstractStoreProvider::class)) {
    $ret[] = (new \Xypp\Store\Extend\StoreItemProvider())
        ->provide(StoreProvider::class);
}
return $ret;
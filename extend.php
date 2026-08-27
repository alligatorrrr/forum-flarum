<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Flarum\Extend;

return [
    new ClarkWinkelmann\LocalExtenders\FollowAfterStart(),
    new ClarkWinkelmann\LocalExtenders\RememberMeByDefault(),

    (new Blomstra\Redis\Extend\Redis([
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ]))
    ->useDatabaseWith('cache', 1)
    ->useDatabaseWith('queue', 2)
    ->useDatabaseWith('session', 3),


];

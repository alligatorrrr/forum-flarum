<?php

/*
 * This file is part of justoverclock/auto-post-badge-pro.
 *
 * Copyright (c) 2021 Marco COlia.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Justoverclock\AutoPostBadge;

use Flarum\Extend;
use Flarum\Api\Event\Serializing;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),
    (new Extend\Settings)
        ->serializeToForum('justoverclock-auto-post-badge-pro.bgColor', 'justoverclock-auto-post-badge-pro.bgColor')
        ->serializeToForum('justoverclock-auto-post-badge-pro.bgColorTwo', 'justoverclock-auto-post-badge-pro.bgColorTwo')
        // gruppo 1
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconOne', 'justoverclock-auto-post-badge-pro.iconOne')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelOne', 'justoverclock-auto-post-badge-pro.labelOne')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromOne', 'justoverclock-auto-post-badge-pro.fromOne')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toOne', 'justoverclock-auto-post-badge-pro.toOne')
        // gruppo 2
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconTwo', 'justoverclock-auto-post-badge-pro.iconTwo')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelTwo', 'justoverclock-auto-post-badge-pro.labelTwo')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromTwo', 'justoverclock-auto-post-badge-pro.fromTwo')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toTwo', 'justoverclock-auto-post-badge-pro.toTwo')
        // gruppo 3
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconTree', 'justoverclock-auto-post-badge-pro.iconTree')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelTree', 'justoverclock-auto-post-badge-pro.labelTree')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromTree', 'justoverclock-auto-post-badge-pro.fromTree')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toTree', 'justoverclock-auto-post-badge-pro.toTree')
        // gruppo 4
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconFour', 'justoverclock-auto-post-badge-pro.iconFour')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelFour', 'justoverclock-auto-post-badge-pro.labelFour')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromFour', 'justoverclock-auto-post-badge-pro.fromFour')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toFour', 'justoverclock-auto-post-badge-pro.toFour')
        // gruppo 5
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconFive', 'justoverclock-auto-post-badge-pro.iconFive')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelFive', 'justoverclock-auto-post-badge-pro.labelFive')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromFive', 'justoverclock-auto-post-badge-pro.fromFive')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toFive', 'justoverclock-auto-post-badge-pro.toFive')
        // gruppo 6
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconSix', 'justoverclock-auto-post-badge-pro.iconSix')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelSix', 'justoverclock-auto-post-badge-pro.labelSix')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromSix', 'justoverclock-auto-post-badge-pro.fromSix')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toSix', 'justoverclock-auto-post-badge-pro.toSix')
        // gruppo 7
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconSeven', 'justoverclock-auto-post-badge-pro.iconSeven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelSeven', 'justoverclock-auto-post-badge-pro.labelSeven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromSeven', 'justoverclock-auto-post-badge-pro.fromSeven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toSeven', 'justoverclock-auto-post-badge-pro.toSeven')
        // gruppo 8
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconEight', 'justoverclock-auto-post-badge-pro.iconEight')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelEight', 'justoverclock-auto-post-badge-pro.labelEight')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromEight', 'justoverclock-auto-post-badge-pro.fromEight')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toEight', 'justoverclock-auto-post-badge-pro.toEight')
        // gruppo 9
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconNine', 'justoverclock-auto-post-badge-pro.iconNine')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelNine', 'justoverclock-auto-post-badge-pro.labelNine')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromNine', 'justoverclock-auto-post-badge-pro.fromNine')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toNine', 'justoverclock-auto-post-badge-pro.toNine')
        // gruppo 10
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconTen', 'justoverclock-auto-post-badge-pro.iconTen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelTen', 'justoverclock-auto-post-badge-pro.labelTen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromTen', 'justoverclock-auto-post-badge-pro.fromTen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toTen', 'justoverclock-auto-post-badge-pro.toTen')
        // gruppo 11
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconEleven', 'justoverclock-auto-post-badge-pro.iconEleven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelEleven', 'justoverclock-auto-post-badge-pro.labelEleven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromEleven', 'justoverclock-auto-post-badge-pro.fromEleven')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toEleven', 'justoverclock-auto-post-badge-pro.toEleven')
        // gruppo 12
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconTwelve', 'justoverclock-auto-post-badge-pro.iconTwelve')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelTwelve', 'justoverclock-auto-post-badge-pro.labelTwelve')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromTwelve', 'justoverclock-auto-post-badge-pro.fromTwelve')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toTwelve', 'justoverclock-auto-post-badge-pro.toTwelve')
        // gruppo 13
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconThirteen', 'justoverclock-auto-post-badge-pro.iconThirteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelThirteen', 'justoverclock-auto-post-badge-pro.labelThirteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromThirteen', 'justoverclock-auto-post-badge-pro.fromThirteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toThirteen', 'justoverclock-auto-post-badge-pro.toThirteen')
        // gruppo 14
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconFourteen', 'justoverclock-auto-post-badge-pro.iconFourteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelFourteen', 'justoverclock-auto-post-badge-pro.labelFourteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromFourteen', 'justoverclock-auto-post-badge-pro.fromFourteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toFourteen', 'justoverclock-auto-post-badge-pro.toFourteen')
        // gruppo 15
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconFifteen', 'justoverclock-auto-post-badge-pro.iconFifteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelFifteen', 'justoverclock-auto-post-badge-pro.labelFifteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromFifteen', 'justoverclock-auto-post-badge-pro.fromFifteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toFifteen', 'justoverclock-auto-post-badge-pro.toFifteen')
        // gruppo 16
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconSixteen', 'justoverclock-auto-post-badge-pro.iconSixteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelSixteen', 'justoverclock-auto-post-badge-pro.labelSixteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromSixteen', 'justoverclock-auto-post-badge-pro.fromSixteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toSixteen', 'justoverclock-auto-post-badge-pro.toSixteen')
        // gruppo 17
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconSeventeen', 'justoverclock-auto-post-badge-pro.iconSeventeen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelSeventeen', 'justoverclock-auto-post-badge-pro.labelSeventeen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromSeventeen', 'justoverclock-auto-post-badge-pro.fromSeventeen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toSeventeen', 'justoverclock-auto-post-badge-pro.toSeventeen')
        // gruppo 18
        ->serializeToForum('justoverclock-auto-post-badge-pro.iconEighteen', 'justoverclock-auto-post-badge-pro.iconEighteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.labelEighteen', 'justoverclock-auto-post-badge-pro.labelEighteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.fromEighteen', 'justoverclock-auto-post-badge-pro.fromEighteen')
        ->serializeToForum('justoverclock-auto-post-badge-pro.toEighteen', 'justoverclock-auto-post-badge-pro.toEighteen')

];

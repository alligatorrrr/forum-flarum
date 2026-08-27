<?php
/*
 * This file is part of xypp/acgembed-rr.
 * 
 * Copyright (c) 2024 小鱼飘飘
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
namespace xypp\acgembed;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class MediaInfo extends AbstractModel
{
    protected $table = 'media_info';
    protected $primaryKey = 'id';
    public $incrementing = true;

    public function postMediaInfo()
    {
        return $this->belongsTo('Flarum\Post\Post', 'post_id');
    }
}

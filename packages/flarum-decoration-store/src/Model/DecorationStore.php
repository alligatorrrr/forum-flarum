<?php

namespace Ziven\decorationStore\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;

class DecorationStore extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_decoration_store';
}

<?php

namespace Ziven\GuaGuaLe\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\User\User;

class GuaGuaLe extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_guaguale';
}

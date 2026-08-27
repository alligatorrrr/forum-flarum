<?php

namespace Ziven\GuaGuaLe\Model;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;

class GuaGuaLeTickets extends AbstractModel{
    use ScopeVisibilityTrait;
    protected $table = 'ziven_guaguale_tickets';
}

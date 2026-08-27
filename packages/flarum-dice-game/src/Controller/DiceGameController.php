<?php

namespace Ziven\DiceGame\Controller;

use Flarum\Frontend\Document;
use Psr\Http\Message\ServerRequestInterface;

class DiceGameController{
    public function __invoke(Document $document, ServerRequestInterface $request){
        return $document;
    }
}

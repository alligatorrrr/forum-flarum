<?php

namespace Ziven\BadgeShop\Controllers;

// Flarum classes
use Flarum\Frontend\Document;
use Psr\Http\Message\ServerRequestInterface;

class BadgeShopIndexController
{
    public function __invoke(Document $document, ServerRequestInterface $request)
    {
        return $document;
    }
}

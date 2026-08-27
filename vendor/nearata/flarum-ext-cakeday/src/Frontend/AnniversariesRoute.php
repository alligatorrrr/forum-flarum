<?php

namespace Nearata\CakeDay\Frontend;

use Flarum\Frontend\Document;
use Flarum\Http\Exception\RouteNotFoundException;
use Flarum\Http\RequestUtil;
use Nearata\CakeDay\Helpers;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnniversariesRoute
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function __invoke(Document $document, ServerRequestInterface $request)
    {
        $actor = RequestUtil::getActor($request);

        if (! Helpers::canView($actor)) {
            throw new RouteNotFoundException();
        }

        $document->title = $this->translator->trans('nearata-cakeday.forum.page.title');
    }
}

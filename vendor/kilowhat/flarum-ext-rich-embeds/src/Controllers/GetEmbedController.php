<?php

namespace Kilowhat\RichEmbeds\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\EmbedSerializer;
use Kilowhat\RichEmbeds\Repositories\EmbedManager;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GetEmbedController extends AbstractShowController
{
    public $serializer = EmbedSerializer::class;

    protected $manager;
    protected $whitelist;

    public function __construct(EmbedManager $manager, WhitelistManager $whitelist)
    {
        $this->manager = $manager;
        $this->whitelist = $whitelist;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $rawUrl = (string)Arr::get($request->getParsedBody(), 'url');
        $refresh = (bool)Arr::get($request->getParsedBody(), 'refresh');

        $this->whitelist->assertValidUrl($rawUrl);

        $url = Embed::normalizeUrl($rawUrl);

        $this->whitelist->assertWhitelisted($url);

        $actor = RequestUtil::getActor($request);

        $actor->assertCan('kilowhat-rich-embeds.useOnOwnPost');

        if ($refresh) {
            $actor->assertCan('kilowhat-rich-embeds.refreshOnAnyPost');
        }

        return $this->manager->firstOrCreate($url, $refresh);
    }
}

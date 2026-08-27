<?php

namespace Ziven\BadgeShop\Api\Controller;

use Ziven\BadgeShop\Api\Serializer\BadgeShopSerializer;
use Ziven\BadgeShop\Model\BadgeShop;

use Flarum\Api\Controller\AbstractListController;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class BadgeShopPurchaseListController extends AbstractListController{
    public $serializer = BadgeShopSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document){
        $id = Arr::get($request->getQueryParams(), 'user_id');
        return BadgeShop::where('user_id', $id)->orderBy('id', 'desc')->get();
    }
}
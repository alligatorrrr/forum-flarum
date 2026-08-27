<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStoreSerializer;
use Ziven\decorationStore\Model\DecorationStore;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Arr;

class DecorationStoreSortController extends AbstractListController{
    public $serializer = DecorationStoreSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document){
        $request->getAttribute('actor')->assertAdmin();
        $decorationStoreOrder = Arr::get($request->getParsedBody(), 'decorationStoreOrder');

        foreach($decorationStoreOrder as $itemID=>$order) {
            DecorationStore::query()->where('id', $itemID)->update(['sort'  => $order]);
        }
    }
}

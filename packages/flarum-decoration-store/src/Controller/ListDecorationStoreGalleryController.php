<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStoreGallerySerializer;
use Ziven\decorationStore\Model\DecorationStoreGallery;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;

class ListDecorationStoreGalleryController extends AbstractListController{
    public $serializer = DecorationStoreGallerySerializer::class;
    protected $url;

    public function __construct(UrlGenerator $url){
        $this->url = $url;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $params = $request->getQueryParams();
        $actor = $request->getAttribute('actor');
        $actor->assertAdmin();
        
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $userID = $actor->id;

        $itemType = $params["itemType"];
        $condition = ["item_type"=>$itemType];

        $decorationStoreGalleryQuery = DecorationStoreGallery::where($condition);
        $decorationStoreGalleryResult = $decorationStoreGalleryQuery
            ->skip($offset)
            ->take($limit + 1)
            ->orderBy('id', 'desc')
            ->get();

        $hasMoreResults = $limit > 0 && $decorationStoreGalleryResult->count() > $limit;

        if($hasMoreResults){
            $decorationStoreGalleryResult->pop();
        }

        $document->addPaginationLinks(
            $this->url->to('api')->route('decorationStore.gallery'),
            $params,
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );


        return $decorationStoreGalleryResult;
    }
}

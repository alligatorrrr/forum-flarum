<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStorePurchaseSerializer;
use Ziven\decorationStore\Model\DecorationStorePurchase;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;

class ListDecorationStorePurchaseHisotryController extends AbstractListController{
    public $serializer = DecorationStorePurchaseSerializer::class;
    public $include = ['decorationData'];
    protected $url;

    public function __construct(UrlGenerator $url){
        $this->url = $url;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $params = $request->getQueryParams();
        $include = $this->extractInclude($request);
        $actor = $request->getAttribute('actor');
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $userID = $actor->id;

        $filter = $params["filter"];
        $filterAllowed = ["item_type","purchase_type"];
        $condition = ["user_id"=>$userID];

        foreach($filter as $key=>$value){
            if($value!=-1 && in_array($key, $filterAllowed)){
                $condition[$key] = $value;
            }
        }

        $decorationStorePurchaseQuery = DecorationStorePurchase::where($condition);
        $decorationStorePurchaseResult = $decorationStorePurchaseQuery
            ->skip($offset)
            ->take($limit + 1)
            ->orderBy('id', 'desc')
            ->get();

        $hasMoreResults = $limit > 0 && $decorationStorePurchaseResult->count() > $limit;

        if($hasMoreResults){
            $decorationStorePurchaseResult->pop();
        }

        $document->addPaginationLinks(
            $this->url->to('api')->route('decorationStore.purchaseHistory'),
            $params,
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );

        $this->loadRelations($decorationStorePurchaseResult, $include);

        return $decorationStorePurchaseResult;
    }
}

<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStoreSerializer;
use Ziven\decorationStore\Model\DecorationStore;
use Ziven\decorationStore\Search\DecorationStoreSearcher;

use Flarum\Query\QueryCriteria;
use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;

class ListDecorationStoreController extends AbstractListController{
    public $serializer = DecorationStoreSerializer::class;
    public $include = ['decorationData'];
    protected $url;
    protected $searcher;

    public function __construct(UrlGenerator $url,DecorationStoreSearcher $searcher){
        $this->url = $url;
        $this->searcher = $searcher;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $params = $request->getQueryParams();
        $include = $this->extractInclude($request);
        $actor = $request->getAttribute('actor');
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $currentUserID = $request->getAttribute('actor')->id;
        $sort = $this->extractSort($request);
        $sortIsDefault = $this->sortIsDefault($request);
        $filters = $this->extractFilter($request);

        $condition = [];
        $filterAllowed = ["item_type","purchase_type","isActivate"];

        foreach($filters as $key=>$value){
            if(in_array($key, $filterAllowed)){
                if($value!=-1){
                    $condition[] = ["a.".$key, '=', $value];
                }else{
                    if($key==="isActivate"){
                        $condition[] = ["a.".$key, '!=', 2];
                    }
                }
            }
        }

        // $criteria = new QueryCriteria($actor, $filters, $sort, $sortIsDefault);
        // if (array_key_exists("q", $filters)) {
        //     $results = $this->searcher->search($criteria, $limit, $offset);
        //     app("log")->error(json_encode($filters));
        //     app("log")->error(json_encode($results));
        // }


        $decorationStoreData = DecorationStore::select("a.*","b.id as purchase_id")
            ->from('ziven_decoration_store as a')
            ->leftJoin('ziven_decoration_store_purchase as b', function($join) use($currentUserID){
                 $join->on('a.id', 'b.item_id');
                 $join->where('b.user_id',$currentUserID);
             })
            ->where($condition)
            ->groupBy('a.id')
            ->skip($offset)
            ->take($limit + 1)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // $decorationStoreData = DecorationStore::where($condition)->orderBy('sort', 'asc')->get();

        $hasMoreResults = $limit > 0 && $decorationStoreData->count() > $limit;

        if($hasMoreResults){
            $decorationStoreData->pop();
        }

        $document->addPaginationLinks(
            $this->url->to('api')->route('decorationStore.get'),
            $params,
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );

        return $decorationStoreData;
    }
}

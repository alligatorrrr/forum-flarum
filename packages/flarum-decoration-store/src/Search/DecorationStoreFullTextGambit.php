<?php

namespace Ziven\decorationStore\Search;

use Flarum\Search\GambitInterface;
use Flarum\Search\SearchState;
use Ziven\decorationStore\DecorationStoreRepository;

class DecorationStoreFullTextGambit implements GambitInterface{
    
    protected $decorationStore;
    public function __construct(DecorationStoreRepository $decorationStore){
        $this->decorationStore = $decorationStore;
    }

    public function apply(SearchState $search, $searchValue){
        app("log")->error("aaaa ".$searchValue);
        $search->getQuery()->where('item_title', 'like', "%{$searchValue}%");
    }
}

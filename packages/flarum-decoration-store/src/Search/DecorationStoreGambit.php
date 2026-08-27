<?php

namespace Ziven\decorationStore\Search;

use Flarum\Search\AbstractRegexGambit;
use Flarum\Search\SearchState;
use Flarum\Settings\SettingsRepositoryInterface;

class DecorationStoreGambit extends AbstractRegexGambit{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings){
        $this->settings = $settings;
    }

    protected function getGambitPattern(){
        return 'is:decorationStore';
    }

    protected function conditions(SearchState $search, array $matches, $negate){
        app("log")->error(json_encode($search));
        app("log")->error(json_encode($matches));
        app("log")->error(json_encode($negate));
        $search->getQuery();
    }
}

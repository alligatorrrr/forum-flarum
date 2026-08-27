<?php
namespace Ziven\decorationStore\Search;

use Flarum\Search\AbstractSearcher;
use Flarum\Search\GambitManager;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;

use Ziven\decorationStore\DecorationStoreRepository;

class DecorationStoreSearcher extends AbstractSearcher{
    protected $events;
    protected $decorationStore;

    public function __construct(DecorationStoreRepository $decorationStore, Dispatcher $events, GambitManager $gambits, array $searchMutators){
        parent::__construct($gambits, $searchMutators);

        $this->events = $events;
        $this->decorationStore = $decorationStore;
    }

    protected function getQuery(User $actor): Builder{
        return $this->decorationStore->query();
    }
}

<?php

namespace Ziven\decorationStore;

use Illuminate\Database\Eloquent\Builder;

class DecorationStoreRepository{
    public function query(){
        return Model\DecorationStore::query();
    }
}

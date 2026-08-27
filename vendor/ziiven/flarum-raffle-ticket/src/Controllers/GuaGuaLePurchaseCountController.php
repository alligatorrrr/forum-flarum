<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLePurchaseCountSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchaseCount;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GuaGuaLePurchaseCountController extends AbstractListController{
    public $serializer = GuaGuaLePurchaseCountSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $zivenAllowGuaGuaLe = $actor->can('ziven.zivenAllowGuaGuaLe');

        if($zivenAllowGuaGuaLe===false){
            return [];
        }
        
        $userID = $actor->id;
        
        if(isset($userID)){
            $guagualePurchaseCount = GuaGuaLePurchaseCount::select("gua_id as id","total_pruchase_count")->where(["user_id"=>$userID])->get();
            return $guagualePurchaseCount;
        }
    }
}

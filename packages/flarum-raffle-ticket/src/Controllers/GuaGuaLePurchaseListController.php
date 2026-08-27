<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLePurchaseSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchase;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GuaGuaLePurchaseListController extends AbstractListController{
    public $serializer = GuaGuaLePurchaseSerializer::class;
    public $include = ['purchasedUser'];

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $zivenAllowGuaGuaLe = $actor->can('ziven.zivenAllowGuaGuaLe');

        if($zivenAllowGuaGuaLe===false){
            return [];
        }

        $params = $request->getQueryParams();
        $userID = $actor->id;
        $guaID = $params["guaID"];
        
        if(isset($userID) && isset($guaID)){
            $guagualeDetails = GuaGuaLePurchase::where(["opened"=>1,"gua_id"=>$guaID])->orderBy('open_at', 'desc')->limit(30)->get();
            return $guagualeDetails;
        }
    }
}

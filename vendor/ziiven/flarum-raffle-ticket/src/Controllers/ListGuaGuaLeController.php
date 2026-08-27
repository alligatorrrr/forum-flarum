<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLeSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLe;

use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListGuaGuaLeController extends AbstractListController{
    public $serializer = GuaGuaLeSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $zivenAllowGuaGuaLe = $actor->can('ziven.zivenAllowGuaGuaLe');

        if($zivenAllowGuaGuaLe===false){
            return [];
        }

        $guagualeData = GuaGuaLe::where(["activated"=>1])->orderBy('id', 'desc')->get();
        return $guagualeData;
    }
}

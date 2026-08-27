<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLeSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLe;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchase;
use Ziven\GuaGuaLe\Model\GuaGuaLeTickets;

use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class GuaGuaLeUpdateController extends AbstractCreateController{
    public $serializer = GuaGuaLeSerializer::class;
    protected $translator;

    public function __construct(Translator $translator){
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $actor->assertAdmin();
        $guagualeID = Arr::get($request->getQueryParams(), 'id');

        if(!isset($guagualeID)){
            $errorMessage = 'ziven-guaguale.admin.guaguale-save-error';
        }else{
            $guagualeSaveData = Arr::get($request->getParsedBody(), 'data', null);
            $errorMessage = "";
            $guagualeData = GuaGuaLe::find($guagualeID);

            if(!isset($guagualeData)){
                $errorMessage = 'ziven-guaguale.admin.guaguale-save-error';
            }else{
                if(Arr::has($guagualeSaveData,"attributes.title")){
                    $guagualeData->title = Arr::get($guagualeSaveData,"attributes.title",null);
                    GuaGuaLePurchase::where(['gua_id'=>$guagualeID])->update(['title'=> $guagualeData->title]); 
                }
                if(Arr::has($guagualeSaveData,"attributes.desc")){$guagualeData->desc = Arr::get($guagualeSaveData,"attributes.desc",null);}
                if(Arr::has($guagualeSaveData,"attributes.cost")){$guagualeData->cost = Arr::get($guagualeSaveData,"attributes.cost",1);}
                if(Arr::has($guagualeSaveData,"attributes.limit")){$guagualeData->limit = Arr::get($guagualeSaveData,"attributes.limit",0);}
                if(Arr::has($guagualeSaveData,"attributes.settings")){$guagualeData->settings = Arr::get($guagualeSaveData,"attributes.settings",null);}
                if(Arr::has($guagualeSaveData,"attributes.image")){$guagualeData->image = Arr::get($guagualeSaveData,"attributes.image",null);}
                if(Arr::has($guagualeSaveData,"attributes.color")){$guagualeData->color = Arr::get($guagualeSaveData,"attributes.color",null);}
                if(Arr::has($guagualeSaveData,"attributes.activated")){
                    $guagualeData->activated = Arr::get($guagualeSaveData,"attributes.activated",1);
                    GuaGuaLeTickets::where("gua_id",$guagualeID)->delete();
                }

                $guagualeData->save();

                return $guagualeData;
            }
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

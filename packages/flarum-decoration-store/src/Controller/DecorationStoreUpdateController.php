<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStoreSerializer;
use Ziven\decorationStore\Model\DecorationStore;
use Ziven\decorationStore\Model\DecorationStoreGallery;
use Ziven\decorationStore\Helpers\CommonHelper;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Arr;

class DecorationStoreUpdateController extends AbstractCreateController{
    public $serializer = DecorationStoreSerializer::class;
    protected $translator;

    public function __construct(Translator $translator){
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $actor->assertAdmin();
        $itemID = Arr::get($request->getQueryParams(), 'id');

        if(!isset($itemID)){
            $errorMessage = 'ziven-decoraton-store.lib.save-error';
        }else{
            $decorationStoreSaveData = Arr::get($request->getParsedBody(), 'data', null);
            $errorMessage = "";
            $decorationStoreData = DecorationStore::find($itemID);

            if(!isset($decorationStoreData)){
                $errorMessage = 'ziven-decoraton-store.lib.save-error';
            }else{
                if(Arr::has($decorationStoreSaveData,"attributes.item_title")){
                    $decorationStoreData->item_title = Arr::get($decorationStoreSaveData,"attributes.item_title",null);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_desc")){
                    $decorationStoreData->item_desc = Arr::get($decorationStoreSaveData,"attributes.item_desc",null);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_amount")){
                    $decorationStoreData->item_amount = Arr::get($decorationStoreSaveData,"attributes.item_amount",0);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_cost")){
                    $decorationStoreData->item_cost = Arr::get($decorationStoreSaveData,"attributes.item_cost",1);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_discount")){
                    $decorationStoreData->item_discount = Arr::get($decorationStoreSaveData,"attributes.item_discount",0);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_discount_days")){
                    $decorationStoreData->item_discount_days = Arr::get($decorationStoreSaveData,"attributes.item_discount_days",0);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_type")){
                    $decorationStoreData->item_type = Arr::get($decorationStoreSaveData,"attributes.item_type","avatarFrame");
                }
                if(Arr::has($decorationStoreSaveData,"attributes.purchase_type")){
                    $decorationStoreData->purchase_type = Arr::get($decorationStoreSaveData,"attributes.purchase_type","onetime");
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_label_recommend")){
                    $decorationStoreData->item_label_recommend = Arr::get($decorationStoreSaveData,"attributes.item_label_recommend",0);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_label_popular")){
                    $decorationStoreData->item_label_popular = Arr::get($decorationStoreSaveData,"attributes.item_label_popular",0);
                }
                if(Arr::has($decorationStoreSaveData,"attributes.item_property")){
                    $itemProperty = Arr::get($decorationStoreSaveData,"attributes.item_property",null);
                    $itemPropertyImageFilenameO = (new CommonHelper)->getPropertyImageFileName($decorationStoreData->item_property);
                    $itemPropertyImageFilenameN = (new CommonHelper)->getPropertyImageFileName($itemProperty);

                    if($itemPropertyImageFilenameO!==$itemPropertyImageFilenameN){
                        DecorationStoreGallery::where("url",$itemPropertyImageFilenameO)->decrement("count");
                        DecorationStoreGallery::where("url",$itemPropertyImageFilenameN)->increment("count");
                    }

                    $decorationStoreData->item_property = $itemProperty;
                }
                if(Arr::has($decorationStoreSaveData,"attributes.isActivate")){
                    $isActivate = Arr::get($decorationStoreSaveData,"attributes.isActivate",0);
                    $itemPropertyImageFilenameO = (new CommonHelper)->getPropertyImageFileName($decorationStoreData->item_property);

                    if($isActivate==2){
                        DecorationStoreGallery::where("url",$itemPropertyImageFilenameO)->decrement("count");
                    }

                    $decorationStoreData->isActivate = $isActivate;
                }

                $decorationStoreData->save();

                return $decorationStoreData;
            }
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

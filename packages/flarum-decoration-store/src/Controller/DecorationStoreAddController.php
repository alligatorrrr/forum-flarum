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
use Illuminate\Support\Carbon;

class DecorationStoreAddController extends AbstractCreateController{
    public $serializer = DecorationStoreSerializer::class;
    protected $settings;
    protected $translator;

    public function __construct(Translator $translator,SettingsRepositoryInterface $settings){
        $this->settings = $settings;
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $actor->assertAdmin();

        $requestData = $request->getParsedBody()['data']['attributes'];
        $errorMessage = "";

        if(!isset($requestData)){
            $errorMessage = 'ziven-decoraton-store.lib.save-error';
        }else{
            $settingTimezone = (new CommonHelper)->getSettingTimezone();
            $item_property = $requestData['item_property'];
            $itemPropertyImageFilenameN = (new CommonHelper)->getPropertyImageFileName($item_property);
            DecorationStoreGallery::where("url",$itemPropertyImageFilenameN)->increment("count");

            $decorationStoreData = new DecorationStore();
            $decorationStoreData->item_title = $requestData['item_title'];
            $decorationStoreData->item_desc = $requestData['item_desc'];
            $decorationStoreData->item_cost = $requestData['item_cost'];
            $decorationStoreData->item_discount = $requestData['item_discount'];
            $decorationStoreData->item_discount_days = $requestData['item_discount_days'];
            $decorationStoreData->item_amount = $requestData['item_amount'];
            $decorationStoreData->item_type = $requestData['item_type'];
            $decorationStoreData->item_label_recommend = $requestData['item_label_recommend'];
            $decorationStoreData->item_label_popular = $requestData['item_label_popular'];
            $decorationStoreData->purchase_type = $requestData['purchase_type'];
            $decorationStoreData->item_property = $item_property;
            $decorationStoreData->assigned_at = Carbon::now($settingTimezone);
            $decorationStoreData->isActivate = $requestData['isActivate'];
            $decorationStoreData->save();

            return $decorationStoreData;
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

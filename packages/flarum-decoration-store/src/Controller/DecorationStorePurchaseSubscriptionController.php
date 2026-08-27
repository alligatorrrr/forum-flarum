<?php

namespace Ziven\decorationStore\Controller;

use Ziven\decorationStore\Serializer\DecorationStorePurchaseSerializer;
use Ziven\decorationStore\Model\DecorationStore;
use Ziven\decorationStore\Model\DecorationStorePurchase;

use Flarum\User\User;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;

class DecorationStorePurchaseSubscriptionController extends AbstractCreateController{
    public $serializer = DecorationStorePurchaseSerializer::class;
    public $include = ['decorationData'];
    protected $translator;

    public function __construct(Translator $translator){
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $requestData = $request->getParsedBody()['data']['attributes'];
        $purchaseID = Arr::get($request->getParsedBody(), 'purchaseID');
        $purchaseStatus = Arr::get($request->getParsedBody(), 'purchaseStatus');
        $purchaseExpired = Arr::get($request->getParsedBody(), 'purchaseExpired');

        $currentUserID = $request->getAttribute('actor')->id;
        $errorMessage = "";

        $purchaseData = DecorationStorePurchase::find($purchaseID);

        if(!isset($purchaseData)){
            $errorMessage = 'ziven-decoration-store.lib.save-error';
        }else{
            $itemID = $purchaseData->item_id;
            $userID = $purchaseData->user_id;

            if($currentUserID!==$userID){
                $errorMessage = 'ziven-decoration-store.lib.save-error';
            }else{
                $itemData = DecorationStore::find($itemID);

                if(isset($itemData)){
                    $itemType = $itemData->item_type;
                    $purchaseData->is_expired = intval($purchaseExpired)===1?1:0;

                    if($purchaseData->is_expired===1){
                        if($purchaseData->item_status===1){
                            $currentUserData = User::find($currentUserID);
                            $currentUserData["decoration_".$itemType] = null;
                            $currentUserData->save();
                        }

                        $purchaseData->item_status = 0;
                    }else{
                        $purchaseCost = $purchaseData->purchase_cost;
                        $purchaseDiscount = $purchaseData->purchase_discount;
                        $purchaseCount = $purchaseData->item_count;
                        $purchaseActualCost = $purchaseCost-($purchaseCost*$purchaseDiscount);

                        $currentUserData = User::find($currentUserID);
                        $currentUserMoneyRemain = $currentUserData->money-($purchaseActualCost*$purchaseCount);

                        if($currentUserMoneyRemain<0){
                            $errorMessage = 'ziven-decoration-store.forum.purchase-error-insufficient-fund';
                        }else{
                            $currentUserData->money = $currentUserMoneyRemain;
                            $currentUserData->save();
                        }
                    }

                    $purchaseData->save();

                    $include = $this->extractInclude($request);
                    $this->loadRelations(new Collection([$purchaseData]), $include);

                    return $purchaseData;
                }else{
                    $errorMessage = 'ziven-decoration-store.lib.save-error';
                }
            }
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

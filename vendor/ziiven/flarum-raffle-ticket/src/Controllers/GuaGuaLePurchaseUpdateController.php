<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLePurchaseSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchase;
use Ziven\GuaGuaLe\Notification\GuaGuaLeBlueprint;

use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Notification\NotificationSyncer;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class GuaGuaLePurchaseUpdateController extends AbstractCreateController{
    public $serializer = GuaGuaLePurchaseSerializer::class;
    protected $translator;
    protected $notifications;

    public function __construct(NotificationSyncer $notifications, Translator $translator){
        $this->translator = $translator;
        $this->notifications = $notifications;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $zivenAllowGuaGuaLe = $actor->can('ziven.zivenAllowGuaGuaLe');

        if($zivenAllowGuaGuaLe===false){
            return [];
        }

        $requestData = $request->getParsedBody()['data']['attributes'];
        $guagualePurchaseID = $requestData['guagualePurchaseID'];
        $currentUserID = $actor->id;
        $errorMessage = "";
        $guagualePurchaseData = GuaGuaLePurchase::find($guagualePurchaseID);

        if(!isset($guagualePurchaseData)){
            $errorMessage = 'ziven-guaguale.forum.guaguale-open-error';
        }else{
            if($guagualePurchaseData->opened===0){
                $currentUserData = User::find($currentUserID);
                $guagualePurchaseResult = json_decode($guagualePurchaseData->pruchase_result);
                $pruchaseWinTotal = 0;

                foreach ($guagualePurchaseResult as $key=>$value) {
                    $winPrice = floatval($key);
                    $winAmount = intval($value);

                    if($winPrice!==0){
                        $pruchaseWinTotal+=$winPrice*$winAmount;
                    }
                }

                $currentUserData->money+=$pruchaseWinTotal;
                $currentUserData->save();

                $guagualePurchaseData->pruchase_win_total = $pruchaseWinTotal;
                $guagualePurchaseData->opened = 1;
                $guagualePurchaseData->open_at = Carbon::now('Asia/Shanghai');;
                $guagualePurchaseData->save();

                $this->notifications->sync(new GuaGuaLeBlueprint($guagualePurchaseData),[$currentUserData]);
                return $guagualePurchaseData;
            }else{
                $errorMessage = 'ziven-guaguale.forum.guaguale-open-error-already-opened';
            }
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

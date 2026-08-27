<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLePurchaseSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLe;
use Ziven\GuaGuaLe\Model\GuaGuaLeTickets;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchase;
use Ziven\GuaGuaLe\Model\GuaGuaLePurchaseCount;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\User\User;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

class GuaGuaLePurchaseController extends AbstractCreateController{
    public $serializer = GuaGuaLePurchaseSerializer::class;
    public $include = ['guagualeData'];
    protected $settings;
    protected $translator;

    public function __construct(Translator $translator,SettingsRepositoryInterface $settings){
        $this->settings = $settings;
        $this->translator = $translator;
    }

    protected function data(ServerRequestInterface $request, Document $document){
        $actor = $request->getAttribute('actor');
        $zivenAllowGuaGuaLe = $actor->can('ziven.zivenAllowGuaGuaLe');

        if($zivenAllowGuaGuaLe===false){
            return [];
        }

        $requestData = $request->getParsedBody()['data']['attributes'];
        $guagualePurchaseCount = intval($requestData['guagualePurchaseCount']);
        $guagualeID = $requestData['guagualeID'];
        $currentUserID = $actor->id;
        $errorMessage = "";

        if(!isset($guagualePurchaseCount) || !isset($guagualeID) || $guagualePurchaseCount<=0){
            $errorMessage = 'ziven-guaguale.forum.purchase-error';
        }else{
            // $guagualeData = GuaGuaLe::from('ziven_guaguale as a')
            //     ->leftJoin('ziven_guaguale_purchase_count as b', function($join) use($currentUserID){
            //          $join->on('a.id', 'gua_id');
            //          $join->where('b.user_id',$currentUserID);
            //      })
            //     ->where(["a.activated"=>1,"a.id"=>$guagualeID])
            //     ->first();

            $guagualeData = GuaGuaLe::where(["activated"=>1,"id"=>$guagualeID])->first();

            if(isset($guagualeData)){
                $guagualeTotalPurchasedCount = $guagualeData->purchased+$guagualePurchaseCount;
                $guagualePurchasedAmount = $guagualeData->amount;

                if($guagualeTotalPurchasedCount<=$guagualePurchasedAmount){ 
                    $guagualeTitle = $guagualeData->title;
                    $guagualeCost = $guagualeData->cost;
                    $guagualeCostTotal = $guagualeCost*$guagualePurchaseCount;
                    $guagualeLimit = $guagualeData->limit;
                    $currentUserData = User::find($currentUserID);
                    $currentUserMoneyRemain = $currentUserData->money-$guagualeCostTotal;

                    $guagualeUserPurchasedData = GuaGuaLePurchaseCount::where(["user_id"=>$currentUserID,"gua_id"=>$guagualeID])->first();
                    $guagualeUserPurchasedCount = $guagualeUserPurchasedData->total_pruchase_count;

                    if(is_null($guagualeUserPurchasedCount)){
                        $guagualeUserPurchasedCount = 0;
                    }

                    if($currentUserMoneyRemain<0){
                        $errorMessage = 'ziven-guaguale.forum.purchase-error-insufficient-fund';
                    }else{
                        if($guagualeLimit>0 && $guagualeLimit<$guagualeUserPurchasedCount+$guagualePurchaseCount){
                            $errorMessage = 'ziven-guaguale.forum.guaguale-purchase-exceed-limit';
                        }else{
                            $guagualeData->purchased = $guagualeTotalPurchasedCount;
                            $guagualeData->save();

                            $matchCondition = ['user_id'=>$currentUserID,'gua_id'=>$guagualeID];
                            GuaGuaLePurchaseCount::updateOrCreate($matchCondition,['total_pruchase_count'=>GuaGuaLePurchaseCount::raw('total_pruchase_count + '.$guagualePurchaseCount)]);

                            $guagualeTicketsFlag = $currentUserID."_".rand(10000, 99999);
                            $guagualeTakeTickets = GuaGuaLeTickets::where(['gua_id'=>$guagualeID,'flag'=>''])->limit($guagualePurchaseCount)->update(['flag'=> $guagualeTicketsFlag]); 
                            $guagualeTickets = GuaGuaLeTickets::where(['flag'=>$guagualeTicketsFlag])->get();
                            GuaGuaLeTickets::where(['flag'=>$guagualeTicketsFlag])->delete();

                            $purchaseResult = array("0"=>0);

                            foreach($guagualeTickets as $key => $tickets) {
                                $ticketValue = strval($tickets->value);
                                if(!isset($purchaseResult[$ticketValue])){ $purchaseResult[$ticketValue] = 0; }
                                $purchaseResult[$ticketValue]+=1;
                            }

                            $defaultTimezone = 'Asia/Shanghai';
                            $settingTimezone = $this->settings->get('moneyTransfer.moneyTransferTimeZone', $defaultTimezone);

                            if(!in_array($settingTimezone, timezone_identifiers_list())) {
                                $settingTimezone = $defaultTimezone;
                            }

                            $guaguaLePurchase = new GuaGuaLePurchase();
                            $guaguaLePurchase->title = $guagualeTitle;
                            $guaguaLePurchase->gua_id = $guagualeID;
                            $guaguaLePurchase->user_id = $currentUserID;
                            $guaguaLePurchase->pruchase_count = $guagualePurchaseCount;
                            $guaguaLePurchase->pruchase_cost = $guagualeCost;
                            $guaguaLePurchase->pruchase_cost_total = $guagualeCostTotal;
                            $guaguaLePurchase->opened = 0;
                            $guaguaLePurchase->pruchase_result = json_encode($purchaseResult);
                            $guaguaLePurchase->assigned_at = Carbon::now($settingTimezone);
                            $guaguaLePurchase->save();

                            $currentUserData->money = $currentUserMoneyRemain;
                            $currentUserData->save();

                            $include = $this->extractInclude($request);
                            $this->loadRelations(new Collection([$guaguaLePurchase]), $include);

                            return $guaguaLePurchase;
                        }
                    }
                }else{
                    $errorMessage = 'ziven-guaguale.forum.purchase-error-not-enough-tickets';
                }
            }else{
                $errorMessage = 'ziven-guaguale.forum.purchase-error';
            }
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

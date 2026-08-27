<?php

namespace Ziven\GuaGuaLe\Controllers;

use Ziven\GuaGuaLe\Serializer\GuaGuaLeSerializer;
use Ziven\GuaGuaLe\Model\GuaGuaLe;
use Ziven\GuaGuaLe\Model\GuaGuaLeTickets;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Support\Carbon;

class GuaGuaLeAddController extends AbstractCreateController{
    public $serializer = GuaGuaLeSerializer::class;
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
            $errorMessage = 'ziven-guaguale.admin.guaguale-add-error';
        }else{
            $defaultTimezone = 'Asia/Shanghai';
            $settingTimezone = $this->settings->get('ziven-guaguale.guagualeTimezone', $defaultTimezone);

            if(!in_array($settingTimezone, timezone_identifiers_list())) {
                $settingTimezone = $defaultTimezone;
            }

            $guaguaLeData = new GuaGuaLe();
            $guaguaLeData->title = $requestData['title'];
            $guaguaLeData->desc = $requestData['desc'];
            $guaguaLeData->image = $requestData['image'];
            $guaguaLeData->color = $requestData['image']?null:$requestData['color'];
            $guaguaLeData->amount = $requestData['amount'];
            $guaguaLeData->cost = $requestData['cost'];
            $guaguaLeData->limit = $requestData['limit'];
            $guaguaLeData->settings = $requestData['settings'];
            $guaguaLeData->activated = 1;
            $guaguaLeData->assigned_at = Carbon::now($settingTimezone);
            $guaguaLeData->save();

            $guagualeID = $guaguaLeData->id;
            $guagualeSettings = json_decode($requestData['settings']);
            $guagualeSettingsRatio = $guagualeSettings->ratio;
            $guagualeTicketList = array();

            foreach ($guagualeSettingsRatio as $key=>$value) {
                $winPrice = floatval($key);
                $winAmount = intval($value);

                for($i=0;$i<$winAmount;$i++){
                    array_push($guagualeTicketList,array("gua_id"=>$guagualeID,"value"=>$winPrice));
                }
            }

            shuffle($guagualeTicketList);
            GuaGuaLeTickets::insert($guagualeTicketList);

            return $guaguaLeData;
        }

        if($errorMessage!==""){
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]); 
        }
    }
}

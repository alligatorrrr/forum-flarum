<?php
namespace Ziven\decorationStore\Helpers;

use Flarum\Settings\SettingsRepositoryInterface;

class CommonHelper {
    public function getPropertyImageFileName($item_property){
      $item_property_image_filename = null;

      if($item_property){
         $item_property_json = json_decode($item_property);
         $item_property_image = $item_property_json->image;
         $item_property_image_explode = explode("/", $item_property_image);
         $item_property_image_explode_count = count($item_property_image_explode);

         if($item_property_image_explode_count>0){
             $item_property_image_filename = $item_property_image_explode[$item_property_image_explode_count-1];
         }
      }
      
      return $item_property_image_filename;
    }

    public function getSettingTimezone(){
        $settings = resolve(SettingsRepositoryInterface::class);
        $defaultTimezone = 'Asia/Shanghai';
        $settingTimezone = $settings->get('ziven-decoraton-store.decorationStoreTimezone', $defaultTimezone);

        if(!in_array($settingTimezone, timezone_identifiers_list())) {
            $settingTimezone = $defaultTimezone;
        }

        return $settingTimezone;
    }

    public function getActualCost($itemData,$itemCount,$type){
        if($type==="store"){
            $itemCost = $itemData->item_cost;
            $itemDiscount = $itemData->item_discount;
        }else{
            $itemCost = $itemData->purchase_cost;
            $itemDiscount = $itemData->purchase_discount;
        }

        return ($itemCost-($itemCost*($itemDiscount/100)))*$itemCount;
    }
}
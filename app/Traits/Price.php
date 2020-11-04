<?php

namespace App\Traits;

use App\Models\Master\Setting;
use Illuminate\Support\Facades\DB;

trait Price{

    /**
     * In Meter
     *
     * @param $range
     * @return float|int
     */
    function getShippingCharge($range){
        $range = $range / 1000; // Get Kilometer

        $setting = Setting::where(['key'=>'ongkir'])->first();

        $range = round($range,0);

        if($range <= $setting->value['minimum-distance']){
            return $setting->value['minimum-price'];
        }else{
            return $setting->value['minimum-price'] + ($setting->value['add-price'] *
                    (($range - $setting->value['minimum-distance'])/$setting->value['add-distance']));
        }
    }

}

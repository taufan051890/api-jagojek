<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/31/20, 2:43 PM
 *
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use Carbon\Carbon;

class MasterVehicleYearController extends Controller
{
    use JsonResponse;

    public function get(){

        $now = Carbon::now()->year;
        $minimum = 10;

        $lists = range($now,$now-$minimum);

        return $this->json200($lists);
    }
}

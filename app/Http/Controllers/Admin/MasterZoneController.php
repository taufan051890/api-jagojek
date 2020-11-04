<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/31/20, 3:32 PM
 *
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Master\City;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterZoneController extends Controller
{
    use JsonResponse;

    public function getCityProvince(Request $request)
    {
        try {
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $search = $request->search ?? '';

            $cities = City::select('zone_cities.id as id', DB::raw("CONCAT(city_name,', ',province_name) as text"))
                ->join('master.zone_provinces as zp','zp.id','=','zone_cities.province_id')
                ->offset($offset)
                ->limit($limit);

            if ($search) {
                $cities->where('city_name', '~*', $search);
            }

            return $this->json200($cities->get());
        }catch (\Exception $e){
            return $this->json500($e->getMessage());
        }

    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Traits\JsonResponse;
use App\Traits\Price;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    use JsonResponse, Price;

    function __construct()
    {
        //$this->preparePriceTrait();
    }

    private function prepareDataOutlet($request,$join=null){
        $longitude = $request->input('long');
        $latitude = $request->input('lat');
        $offset = 0;
        $limit = 0;

        if($request->has('offset')){
            $offset = $request->input('offset');
        }

        if($request->has('limit')){
            $limit = $request->input('limit');
        }

        $select = [
            'idPartnerOutlet as id',
            'NameOutlet as nama',
            'PhotoOutlet as gambar',
            'DescOutlet as deskripsi'
        ];

        $data = DB::table('humanCapital.hcPartnerOutlet')
            ->select($select);

        //Rating
        $data->addSelect(DB::raw('(SELECT ROUND(COALESCE(AVG("Rate"),0),1)
            FROM "humanCapital"."hcPartnerRate"
            WHERE "fidPartner" = "idPartnerOutlet" AND "Role" = 4 ) as rating'));

        //Distance
        $data->addSelect(DB::raw('ROUND(CAST((ST_DISTANCE("Coordinate",
            ST_GeographyFromText(\'POINT('.$latitude.' '.$longitude.')\'))/1000) as numeric),1) as jarak'));

        //Discount
        $data->addSelect('ListDiscount as listDiscount');

        //Left Join Favorite
        if($join=='favorite'){
            $data->leftJoin('humanCapital.hcOutletFav as fav',function($join){
                $join->on('fav.fidOutlet', '=','humanCapital.hcPartnerOutlet.idPartnerOutlet');
                $join->where('fav.St','10');
            });
        }

        if($join=='order'){
            $data->leftJoin('JagoFood.FoodOrder as food_order',function($join){
                $join->on('food_order.FidOutlet', '=','humanCapital.hcPartnerOutlet.idPartnerOutlet');
                $join->where('food_order.Status',7);
            });
        }

        if($limit>0){
            $data->offset($offset);
            $data->limit($limit);
        }

        return $data;
    }

    private function additionalResult($param){
        for($i=0;$i<count($param);$i++){
            $range = $param[$i]->jarak;
            $param[$i]->ongkir = $this->getShippingCharge($range);
            $param[$i]->jarak = $range.' km';

            //Get Discount List
            if($param[$i]->listDiscount!=null){
                $list_discount = json_decode($param[$i]->listDiscount);
            }else{
                $list_discount = [];
            }


            $param[$i]->listDiscount = DB::table('masterData.Discount')
                ->select('Code as diskon')
                ->whereIn('idDiscount',$list_discount)
                ->get();
        }

        return $param;
    }

    function getNewOutlets(Request $request){
        $data = $this->prepareDataOutlet($request);

        $result = $data->orderBy('JoinDate','DESC')
            ->get();

        $result = $this->additionalResult($result);

        return $this->json200($result);
    }

    function getInDemandOutlets(Request $request){
        $data = $this->prepareDataOutlet($request,'order');

        $result = $data->groupBy("idPartnerOutlet")
            ->orderByRaw('COUNT(food_order."idFoodOrder") DESC')
            ->get();

        $result = $this->additionalResult($result);

        return $this->json200($result);
    }

    function getNearestOutlets(Request $request){

        $data = $this->prepareDataOutlet($request);

        $result = $data->orderByRaw('ST_DISTANCE("Coordinate",
            ST_GeographyFromText(\'POINT('.$request->input('lat').' '
                    .$request->input('long').')\')) ASC')
                ->get();

        $result = $this->additionalResult($result);

        return $this->json200($result);
    }

    function getFavoriteOutlets(Request $request){
        $data = $this->prepareDataOutlet($request,'favorite');

        try{
            $result = $data->groupBy("idPartnerOutlet")
                ->orderByRaw('COUNT(fav."fidOutlet") DESC')
                ->get();

            $result = $this->additionalResult($result);

            return $this->json200($result);
        }catch (\Exception $e){
            return $this->json500($e->getMessage());
        }

    }

    function getOutletInfo(){

    }

    function likeOutlet(){

    }

    function dislikeOutlet(){

    }

    function ratesOutlet(){

    }



}

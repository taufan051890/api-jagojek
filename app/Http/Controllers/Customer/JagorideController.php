<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Rules\FoodExists;
use App\Traits\JsonResponse;
use GoogleMaps;
use App\Traits\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JagorideController extends Controller
{
    use JsonResponse, Price;

    public $customer_id;

    public function __construct()
    {
    	$this->customer_id = request()->user->user_id;
    }

    function getLocation()
    {
        $response = GoogleMaps::load('placeautocomplete')
                    ->setParamByKey('input', 'Vict')
                    ->setParamByKey('types', 'cities')                    
                    ->setParamByKey('language', 'fr') 
                    ->getResponseByKey('predictions.place_id');  
        return $this->json200(['response'=>$response]);
    }


}

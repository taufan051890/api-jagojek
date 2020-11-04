<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Jagofood\Category;
use App\Models\Jagofood\Food;
use App\Models\Jagofood\Love;
use App\Models\Jagofood\Outlet;
use App\Models\Jagofood\Promo;
use App\Models\Jagofood\Rate;
use App\Models\Master\Voucher;
use App\Rules\FoodExists;
use App\Traits\JsonResponse;
use App\Traits\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JagofoodController extends Controller
{
    use JsonResponse, Price;

    public $customer_id;

    public function __construct()
    {
    	$this->customer_id = request()->user->user_id;
    }

    public function love(Request $request)
    {
        $this->validate($request, [
            'outlet_id' => 'required|exists:App\Models\Jagofood\Outlet,id'
        ]);

        $love = Love::firstOrNew([
            'outlet_id' => $request->input('outlet_id'),
            'customer_id' => $this->customer_id
        ]);

        if($love->id){
            $love->status = !$love->status;
        }

        $love->save();

        $love->refresh();

        return $this->json200($love);

    }

    public function rate(Request $request)
    {
        $this->validate($request,[
            'outlet_id' => 'required|exists:App\Models\Jagofood\Outlet,id',
            'rate' => 'required|numeric|in:1,2,3,4,5',
        ]);

        $rate = Rate::firstOrNew([
            'outlet_id' => $request->input('outlet_id'),
            'customer_id' => $this->customer_id
        ]);

        $rate->rate = $request->input('rate');

        $rate->save();
        $rate->refresh();

        return $this->json200($rate);

    }

    public function getOutlet(Request $request,$by)
    {
        $this->validate($request,[
            'latitude' => 'required',
            'longitude' => 'required',
            'offset' => 'numeric',
            'limit' => 'numeric'
        ]);

        $lat = $request->input('latitude');
        $long = $request->input('longitude');
        $offset = $request->input('offset') ?? 0;
        $limit = $request->input('limit') ?? 5;
        $keyword = $request->input('keyword') ?? null;

        $select = [
            'id',
            'name',
            'banner',
            'description',
            'latitude',
            'longitude'
        ];

        $outlet = Outlet::select($select)->withCount('loves');

        //Rating
        $outlet->addSelect(DB::raw('(SELECT COALESCE(AVG("rate"),0)
            FROM jagofood.rates
            WHERE rates.outlet_id = outlet.id ) as rating'));

        //Distance
        $outlet->addSelect(DB::raw("ST_DISTANCE(geo_location,
            ST_GeographyFromText('POINT($long $lat)')) as distance"));

        $outlet->withCount(['order'=>function($query){
            $query->where('status', 20);
        }]);

        //Implement Search
        if($keyword){
            $outlet->where(function($query) use ($keyword){
                $query->where('name','~*',$keyword);
                $query->orWhere('description','~*',$keyword);
                $query->orWhereHas('category',function($query) use ($keyword){
                    $query->where('name','~*',$keyword);
                    $query->orWhereHas('food',function($query) use ($keyword){
                       $query->where('name','~*',$keyword);
                    });
                });
            });
        }

        $outlet->offset($offset);
        $outlet->limit($limit);

        switch($by){
            case 'nearest': {
                $outlet->orderByRaw("ST_DISTANCE(geo_location, ST_GeographyFromText('POINT($long $lat)')) ASC");
                break;
            }
            case 'favorite': {
                $outlet->orderBy('loves_count','desc');
                break;
            }
            case 'newest': {
                $outlet->orderBy('outlet.created_at','desc');
                break;
            }
            case 'healthy': {
                $outlet->where('outlet.is_healthy_menu', true);
                $outlet->orderByRaw("ST_DISTANCE(geo_location, ST_GeographyFromText('POINT($long $lat)')) ASC");
                break;
            }
            case 'promo': {
                $outlet->whereHas('promo',function($query){
                    $query->where('start_at','<=',Carbon::now());
                    $query->where('expired_at','>=',Carbon::now());
                    $query->where('status',true);
                });
                break;
            }
            case 'best_seller' : {
                //Oke;
                $outlet->orderBy('order_count','DESC');
                break;
            }
            case 'other' : {
                $outlet->orderBy(DB::raw("RANDOM()"));
                break;
            }
            case 'reorder' : {
                $outlet->whereHas('transaction', function($transaction) {
                    $transaction->where('status', 20);
                });
            }
        }

        $result = $outlet->get();

        for($i=0;$i<$result->count();$i++){
            $distance = $result[$i]->distance;
            $result[$i]->distance = ($distance < 1000) ? round($distance).' m' : round($distance/1000,1).' km';
            $result[$i]->ongkir = $this->getShippingCharge($distance);

            $result[$i]->usable_promo = Promo::select([
                DB::raw("CONCAT('Diskon ',type,' ',discount) as promo"),
                'type',
                'minimum_order',
                'discount as max_discount',
                'start_at',
                'expired_at'
            ])
                ->where('outlet_id',$result[$i]->id)
                ->whereIn('type',['ongkir','total'])
                ->where('start_at','<=',Carbon::now())
                ->where('expired_at','>=',Carbon::now())
                ->get();
        }

        return $this->json200($result);
    }

    public function getOutletFoodCategory(Request $request)
    {
        $this->validate($request,[
            'outlet_id'=>'required|exists:App\Models\Jagofood\Outlet,id'
        ]);

        $outlet_id = $request->get('outlet_id');

        $category = Category::select('id as category_id','name as category_name')
            ->where('outlet_id',$outlet_id)
            ->where('status',true)
            ->get();

        return $this->json200($category);
    }

    public function getFoodByCategory(Request $request)
    {
        try {
            $this->validate($request, [
                'category_id' => 'required|exists:App\Models\Jagofood\Category,id',
                'offset' => 'numeric',
                'limit' => 'numeric'
            ]);

            $category_id = $request->get('category_id');
            $offset = $request->get('offset') ?? 0;
            $limit = $request->get('limit') ?? 10;

            $food = Food::select('id','name','description','preview','price')
                ->where('category_id', $category_id)
                ->where('status', true)
                ->offset($offset)
                ->limit($limit)
                ->get();

            foreach($food as $f){
                $f->promo_price = $f->price;
            }

            return $this->json200($food);
        }catch (\Exception $e){
            return $this->json500($e->getMessage());
        }
    }

    public function getFoodPromo(Request $request) {
        $this->validate($request,[
            'offset' => 'numeric',
            'limit' => 'numeric'
        ]);

        $lat = $request->input('latitude');
        $long = $request->input('longitude');
        $offset = $request->get('offset') ?? 0;
        $limit = $request->get('limit') ?? 10;

        $food = Food::select('food.id', 'category_id', 'food.name', 'food.preview', 'food.description', 'food.price', 'outlet.id as outlet_id', 'outlet.name as outlet_name', 'outlet.geo_location');

        $food->addSelect(DB::raw('(SELECT COALESCE(AVG("rate"),0)
            FROM jagofood.rates
            WHERE rates.outlet_id = outlet.id ) as rating'));

        //Distance
        $food->addSelect(DB::raw("ST_DISTANCE(geo_location,
            ST_GeographyFromText('POINT($long $lat)')) as distance"));

        $food->whereHas('promo', function($promo) {
                $promo->where('start_at','<=',Carbon::now());
                $promo->where('expired_at','>=',Carbon::now());
                $promo->where('status',true);
            })
            ->leftJoin('jagofood.category as category', 'food.category_id', '=', 'category.id')
            ->leftJoin('jagofood.outlet as outlet', 'category.outlet_id', '=', 'outlet.id');

        $result = $food->offset($offset)
                ->limit($limit)
                ->get();    

        for($i=0;$i<$result->count();$i++){
            $distance = $result[$i]->distance;
            $result[$i]->distance = ($distance < 1000) ? round($distance).' m' : round($distance/1000,1).' km';
            $result[$i]->ongkir = $this->getShippingCharge($distance);

            $result[$i]->usable_promo = Promo::select([
                DB::raw("CONCAT('Diskon ',type,' ',discount) as promo"),
                'type',
                'minimum_order',
                'discount as max_discount',
                'start_at',
                'expired_at'
            ])
                ->where('outlet_id',$result[$i]->id)
                ->whereIn('type',['ongkir','total'])
                ->where('start_at','<=',Carbon::now())
                ->where('expired_at','>=',Carbon::now())
                ->get();
        }

        return $this->json200($result);
    }

    public function getFavouriteOutlet() {
        $outlet = Outlet::whereHas('loves', function($query) {
            $query->where('customer_id', $this->customer_id);
        })->get();

        return $this->json200($outlet);
    }

    public function search(Request $request) {
        $this->validate($request,[
            'keyword' => 'required|string',
            'offset' => 'numeric',
            'limit' => 'numeric'
        ]);
        

        $keyword = $request->input('keyword');
        $offset = $request->get('offset');
        $limit = $request->get('limit');

        $outlet = Outlet::where('name','~*',$keyword)
                ->orWhere('description','~*',$keyword)
                ->orWhereHas('category',function($query) use ($keyword){
                    $query->where('name','~*',$keyword);
                    $query->orWhereHas('food',function($query) use ($keyword){
                    $query->where('name','~*',$keyword);
                    });
                })                
                ->offset($offset)
                ->limit($limit)
                ->get();

        return $this->json200($outlet);
    }


}

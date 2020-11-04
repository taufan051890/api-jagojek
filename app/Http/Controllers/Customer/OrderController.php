<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/3/20, 9:49 PM
 *
 */

namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Controller;
use App\Models\Jagofood\Food;
use App\Models\Jagofood\Outlet;
use App\Models\Transaction\FoodOrder;
use App\Models\Master\Voucher;
use App\Rules\FoodExists;
use App\Traits\JsonResponse;
use App\Traits\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use JsonResponse,Price;

    private $customer_id;

    public function __construct()
    {
        $this->customer_id = request()->user->user_id;
    }

    public function placeOrderFood(Request $request)
    {
        $this->validate($request,[
            'outlet_id' => 'required|exists:App\Models\Jagofood\Outlet,id',
            'my_address' => 'required|string|max:250',
            'my_latitude' => 'required|numeric',
            'my_longitude' => 'required|numeric',
            'my_voucher' => 'string',
            'my_payment_method' => 'required|in:cash,jagocoin',
            'my_order' => 'required|array',
            'my_order.*.food_id' => ['required',new FoodExists($request->outlet_id)],
            'my_order.*.qty' => 'required|integer|min:1',
            'my_order.*.note' => 'string|max:200'
        ]);

        $data = $request->all();
        $lat = $request->my_latitude;
        $long = $request->my_longitude;

        $outlet = Outlet::select(
            [
                DB::raw("ST_DISTANCE(geo_location,
                ST_GeographyFromText('POINT($long $lat)')) as distance"),
            ])->where('id', $request->outlet_id)->first();

        $range = $outlet->distance;
        $ongkir_price = $this->getShippingCharge($range);

        DB::beginTransaction();

        $now = date('Y-m-d');
        try{
            // Initialize Food Order
            $food_order = new FoodOrder();
            $food_order->customer_id = $this->customer_id;
            $food_order->driver_id = null;
            $food_order->outlet_id = $request->input('outlet_id');
            $food_order->status = 0; // 0 is Waiting Order
            $food_order->order_at = Carbon::now();

            // TODO : CHECK PAYMENT METHOD IF JAGOCOIN
            $food_order->payment_type = $request->input('my_payment_method');
            $food_order->total_food_price = 0;
            
            $food_order->final_price = 0;

            $food_order->customer_address = $request->input('my_address');
            $food_order->customer_latitude = $request->input('my_latitude');
            $food_order->customer_longitude = $request->input('my_longitude');

            // Price Value
            $total_food_price = 0;

            // Get Food
            $orders = $request->input('my_order');
            $food_cart = [];

            // Begin Fetch Order
            $food_discount = 0;
            for($i=0;$i<count($orders);$i++){
                $order = $orders[$i];
                $db_food = Food::find($order['food_id']);

                // TODO : What If Food Has Discount
                $temp = [
                    'food_order_id' => $food_order->id,
                    'food_id' => $db_food->id,
                    'name' => $db_food->name,
                    'price' => $db_food->price,
                    'qty' => $order['qty'],
                    'total_price' => $db_food->price * $order['qty'],
                    'note' => $order['note']
                ];

                array_push($food_cart,$temp);
                $total_food_price += $temp['total_price'];
            }

            $total_discount = 0;

            /* PROMO TOTAL */
            $promo_total = Promo::where('type', 'ongkir')
                            ->where('outlet_id', $this->outlet_id)
                            ->where('start_at', '<=', $now)
                            ->where('expired_at', '>=', $now)
                            ->first();

            if($promo_total) {
                if($total_food_price > $promo_total->minimum_order) 
                    $total_discount += ($total_food_price - ($total_food_price*$promo_total->discount/100));
            }

            // TODO : WHat if he has Voucher???
            /* PROMO ONGKIR */
            $promo_ongkir = Promo::where('type', 'ongkir')
                            ->where('outlet_id', $this->outlet_id)
                            ->where('start_at', '<=', $now)
                            ->where('expired_at', '>=', $now)
                            ->first();

            if($promo_ongkir) {
                if($total_food_price > $promo_ongkir->minimum_order) 
                    $ongkir_price -= max(0, $promo_ongkir->discount);
            } 

            $food_order->ongkir_price = $ongkir_price;

            if($request->input('voucher')!=null){

                $food_order->discount_code = null;

                // TODO: Implements Voucher Discount ~_~
                $voucher = Voucher::where('code', $request->input('voucher'))
                            ->where('start_at', '<=', $now)
                            ->where('expired_at', '>=', $now)
                            ->where('type', 'food')
                            ->where('minimum_order', '<=', $total_food_price)
                            ->first();
                
                if($voucher) {
                    $food_order->discount_code = $request->input('voucher');

                    $voucher_discount = $total_food_price - ($total_food_price * $voucher->discount / 100);
                    $total_discount += min($voucher->maximum_discount, $voucher_discount);   
                }                
            }
            

            DB::table('transaction.food_order_details')->insert($food_cart);

            $food_order->total_food_price = $total_food_price;
            $food_order->total_discount = $total_discount;
            $food_order->final_price = ( $total_food_price + $ongkir_price ) - $total_discount;
            $food_order->save();

            DB::commit();

            return $this->json200('Pesanan telah dibuat.');

        }catch (\Exception $e){
            DB::rollBack();
            return $this->json500($e->getMessage());
        }

    }

    public function getOrderFood(Request $request) {
        $offset = $request->input('offset') ?? 0;
        $limit = $request->input('limit') ?? 5;

        $order = FoodOrder::select(
                    'id', 
                    'customer_id',
                    'driver_id',
                    'outlet_id', 
                    'order_at', 
                    'customer_address', 
                    'customer_latitude', 
                    'customer_longitude',
                    'status'
                )
                ->withCount(['details as items' => function($query) {
                    $query->select(DB::raw("SUM(qty) as sum"));
                }])
                ->with(['outlet:id,name,address,latitude,longitude', 'driver:id,name,phone_number,avatar'])
                ->where('customer_id', $this->customer_id)
                ->orderBy('order_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

        return $this->json200($order);
    }

    public function getDetailOrderFood($id) {
        $order = FoodOrder::select(
                    'id', 
                    'customer_id',
                    'driver_id',
                    'outlet_id', 
                    'order_at', 
                    'success_at',
                    'payment_type',
                    'total_food_price',
                    'discount_code',
                    'total_discount',
                    'ongkir_price',
                    'final_price',
                    'additional_info',
                    'cancel_reason',
                    'customer_address', 
                    'customer_latitude', 
                    'customer_longitude',
                    'status'
                )
                ->withCount(['details as items' => function($query) {
                    $query->select(DB::raw("SUM(qty) as sum"));
                }])
                ->with([
                    'outlet:id,name,address,latitude,longitude',
                    'driver:id,name,phone_number,avatar',
                    'details:id,food_order_id,name,price,qty,total_price,note'
                ])
                ->where('id', $id)
                ->where('customer_id', $this->customer_id)
                ->first();

        if($order) return $this->json200($order);
        else return $this->json401();
    }
}

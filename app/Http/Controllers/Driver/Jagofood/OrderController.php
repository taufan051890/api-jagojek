<?php

namespace App\Http\Controllers\Driver\Jagofood;


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
    
    private $driver_id;

    public function __construct()
    {
        $this->driver_id = request()->user_id;
    }

    public function getOrderFoodHistory(Request $request) {
		$this->validate($request,[
			'offset' => 'numeric',
			'limit' => 'numeric',
			'status' => 'in:0,10,15,20,30,40,50' // what is this?
			/* 
				0 = Belum diproses
				10 = Cancelled
				15 = Sedang diproses
				20 = Selesai
				30 = Menjemput (pickup)
				40 = Delivering
				50 = Arrived
			*/
		]);

		$offset = $request->input('offset') ?? 0;
		$limit = $request->input('limit') ?? 5;
		$status = $request->input('status') ?? null;
		
		$where = [ 
			['driver_id', $this->driver_id],
			['status', '>', 0]
		];

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
					'customer:id,name,phone_number,avatar'
				]);
		
		if($status !== null) {
			if($status == 0) 
				$order = $order->where('status', 15)->whereNull('driver_id');

			else 
				$order = $order->where($where)->where('status', $status);
		} else {
			$order = $order->where(function($q) use ($where) {
				$q->where($where)->orWhere(function($r) {
					$r->where('status', 15)->whereNull('driver_id');
				});
			});
		}
				
		$order = $order->orderBy('order_at', 'desc')
						->offset($offset)
						->limit($limit)
						->get();

		return $this->json200($order);
    }

    public function acceptOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);

        $order = FoodOrder::where('status', 15)
                            ->whereNull('driver_id')
                            ->find($request->get('order_id'));

        if($order) {
            $order->driver_id = $this->driver_id;
            $order->save();

            return $this->json200("Berhasil menerima pesanan");
        } else {
            return $this->json401();
        }
    }
    
    public function cancelOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer',
            'cancel_reason' => 'required'
        ]);

        $order = FoodOrder::where('driver_id', $this->driver_id)
                            ->where('status', 15)
                            ->find($request->get('order_id'));

        if($order) {
            $order->status = 10;
            $order->cancel_reason = $request->get('cancel_reason');
            $order->save();

            return $this->json200("Berhasil membatalkan pesanan");
        } else {
            return $this->json401();
        }
    }

    public function pickupOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer',
            'driver_latitude' => 'required',
            'driver_longitude' => 'required'
        ]);

		$order = FoodOrder::select("food_order.*", "outlet.geo_location")
							->leftJoin("jagofood.outlet", "outlet.id", "=", "food_order.outlet_id")
							->where('food_order.driver_id', $this->driver_id)
                            ->where('food_order.status', 15)
                            ->whereRaw("ST_DISTANCE(outlet.geo_location, ST_GeographyFromText('POINT(". $request->get('driver_longitude') . " " . $request->get('driver_latitude') . ")')) < 30")
							->where('food_order.id', $request->get('order_id'))
							->first();

        if($order) {
            $order->status = 30;
            $order->save();

            return $this->json200("Berhasil menjemput pesanan");
        } else {
            return $this->json401();
        }
    }

    public function deliverOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);

        $order = FoodOrder::where('driver_id', $this->driver_id)
                            ->where('status', 30)
                            ->find($request->get('order_id'));

        if($order) {
            $order->status = 40;
            $order->save();

            return $this->json200("Berhasil mengantar pesanan");
        } else {
            return $this->json401();
        }
    }

    public function arrivedOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer',
            'driver_latitude' => 'required',
            'driver_longitude' => 'required'
        ]);

        $order = FoodOrder::where('driver_id', $this->driver_id)
                            ->where('status', 40)
                            ->whereRaw("ST_DISTANCE(ST_GeographyFromText('POINT(' || customer_longitude || ' ' || customer_latitude || ')'), ST_GeographyFromText('POINT(". $request->get('driver_longitude') . " " . $request->get('driver_latitude') . ")')) < 30")
                            ->find($request->get('order_id'));

        if($order) {
            $order->status = 50;
            $order->save();

            return $this->json200("Berhasil tiba dilokasi");
        } else {
            return $this->json401();
        }
    }

    public function finishOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);

        $order = FoodOrder::where('driver_id', $this->driver_id)
                            ->where('status', 50)
                            ->find($request->get('order_id'));

        if($order) {
            $order->status = 20;
            $order->save();

            return $this->json200("Pesanan berhasil diselesaikan");
        } else {
            return $this->json401();
        }
    }
}
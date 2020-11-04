<?php
namespace App\Http\Controllers\Jagofood;

use App\Http\Controllers\Controller;
use App\Models\Transaction\FoodOrder;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use JsonResponse;

    private $outlet_id;

    public function __construct()
    {
        $this->outlet_id = request()->user->outlet_id;
    }

    public function acceptOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);

        $order = FoodOrder::where('outlet_id', $this->outlet_id)
                            ->where('status', 0)
                            ->find($request->get('order_id'));

        if($order) {
            $order->status = 15;
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

        $order = FoodOrder::where('outlet_id', $this->outlet_id)
                            ->whereIn('status', [0, 15])
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

    public function historyOrder(Request $request)
    {
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

        $offset = $request->get('offset') ?? 0;
        $limit = $request->get('limit') ?? 0;
        $status = $request->get('status') ?? null;

        $order = $this->OrderResponse();

        if($limit>0) {
            $order->offset($offset);
            $order->limit($limit);
        }

        if($status!=null){
            $order->where('status',$status);
        }

        return $this->json200($order->get());
    }

    // public function detailOrder($id) 
    // {
    //     $order = $this->OrderResponse($id);

    //     return $this->json200($order->first());
    // }

    private function OrderResponse($order_id = null)
    {
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
                    'customer:id,name,phone_number', 
                    'driver:id,name,phone_number,avatar',
                    'details:id,food_order_id,name,price,qty,total_price,note'
                ])
                ->where('outlet_id', $this->outlet_id);

        if($order_id) $order->where('id', $order_id);

        $order->orderBy('order_at', 'desc');

        return $order;
    }

}

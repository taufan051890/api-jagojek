<?php
namespace App\Http\Controllers\Jagomart;

use App\Http\Controllers\Controller;
use App\Models\Jagomart\Item;
use App\Models\Jagomart\Promo;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    use JsonResponse;

    protected $outlet_id;

    public function __construct()
    {
        $this->outlet_id = request()->user->outlet_id;
    }

    public function getPromoHistory(Request $request){

        $promo = Promo::select(
            DB::raw('promo.*'),'item.name as item_name'
            ,'item.price as item_price_before_promo',
        'item.preview as item_preview','item.description as item_desc')
            ->leftJoin('jagomart.item','promo.item_id','=','item.id')
            ->where('outlet_id',$this->outlet_id);

        if($limit = $request->get('limit')){
            $offset = $request->get('offset') ?? 0;

            $promo->limit($limit);

            $promo->offset($offset);

        }

        return $this->json200($promo->orderBy('promo.id','desc')->get());
    }

    public function createPromoProduct(Request $request){
        //Validate Item

        //Check Item Ownership


        $promo = new Promo();

        $promo->outlet_id = $this->outlet_id;
        $promo->item_id = $request->input('item_id');
        $promo->item_price_after_promo = $request->input('price_after_promo');
        $promo->type = 'product';
        $promo->start_at = Carbon::createFromFormat('d/m/Y',$request->input('start_at'))
            ->format('Y-m-d');
        $promo->expired_at = Carbon::createFromFormat('d/m/Y',$request->input('expired_at'))
            ->format('Y-m-d');
        $promo->status = true;
        $promo->minimum_order = null;
        $promo->discount = null;

        $promo->save();

        $promo->refresh();

        return $this->json200($promo);
    }

    public function createPromoTotal(Request $request){
        //Validate Item

        //Check Item Ownership


        $promo = new Promo();

        $promo->outlet_id = $this->outlet_id;
        $promo->item_id = null;
        $promo->item_price_after_promo = null;
        $promo->type = 'total';
        $promo->start_at = Carbon::createFromFormat('d/m/Y',$request->input('start_at'))
            ->format('Y-m-d');
        $promo->expired_at = Carbon::createFromFormat('d/m/Y',$request->input('expired_at'))
            ->format('Y-m-d');
        $promo->discount = $request->input('discount');
        $promo->status = true;
        $promo->minimum_order = $request->input('minimum_price');

        $promo->save();

        $promo->refresh();

        return $this->json200($promo);
    }

    public function createPromoOngkir(Request $request){
        //Validate Item

        //Check Item Ownership


        $promo = new Promo();

        $promo->outlet_id = $this->outlet_id;
        $promo->item_id = null;
        $promo->type = 'ongkir';
        $promo->item_price_after_promo = null;
        $promo->start_at = Carbon::createFromFormat('d/m/Y',$request->input('start_at'))
            ->format('Y-m-d');
        $promo->expired_at = Carbon::createFromFormat('d/m/Y',$request->input('expired_at'))
            ->format('Y-m-d');
        $promo->discount = $request->input('discount');
        $promo->minimum_order = $request->input('minimum_price');
        $promo->status = true;


        $promo->save();

        $promo->refresh();

        return $this->json200($promo);
    }

    public function stopPromo(Request $request){
        $promo = Promo::where('outlet_id',$this->outlet_id)
            ->find($request->input('promo_id'));

        if($promo){
            $promo->status = false;
            $promo->save();

            return $this->json200('Berhasil menghentikan promo.');
        }else{
            return $this->json401();
        }
    }

}

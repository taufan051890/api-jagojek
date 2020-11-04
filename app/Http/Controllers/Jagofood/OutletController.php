<?php
namespace App\Http\Controllers\Jagofood;

use App\Http\Controllers\Controller;
use App\Models\Jagofood\OperationalTime;
use App\Models\Jagofood\Outlet;
use App\Models\Master\Day;
use App\Services\Jagomart\JagomartRequest;
use App\Traits\FileUpload;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{

    use JsonResponse, FileUpload;

    protected $outlet_id;

    public function __construct()
    {
        $this->outlet_id = request()->user->outlet_id;
    }

    public function getInfoOutlet()
    {
        $data = DB::table('jagofood.outlet')
            ->select('outlet.*','user.email as registered_email',
	     'user.phone_number as registered_phone','user.pin as is_pin_set')
            ->join('jagofood.user','user.id','=','outlet.user_id')
	        ->where('outlet.id',$this->outlet_id)
            ->first();

	    $data->is_pin_set = ($data->is_pin_set != null);

        return $this->json200($data);
    }

    public function setOpenStatusOutlet(){
        $outlet = Outlet::find($this->outlet_id);

        if($outlet){
            $outlet->is_open = !$outlet->is_open;

            if($outlet->save()){
                return $this->json200($outlet->is_open);
            }else {
                return $this->json500('Change Status Failed');
            }

        }else{
            return $this->json500('Toko tidak ditemukan.');
        }
    }



    public function changeBanner(Request $request){
        $outlet = Outlet::find($this->outlet_id);

        if($outlet){
            if($outlet->banner!=null){

            }

            $outlet->banner = $this->upload($request->file('banner'),'jagofood/outlet/banner/');
            $outlet->save();

            return $this->json200(['banner' => $outlet->banner]);

        }else{
            return $this->json401();
        }
    }

    public function getOperationalTime(){
        try{
            $outlet = $this->outlet_id;
            $select = [
                'days.id as day_id',
                'days.name',
                'is_24',
                'is_close',
                'is_custom_time',
                'time_slot'
            ];
            $data = Day::select($select)
                ->leftJoin('jagofood.operational_time',function($join) use ($outlet){
                    $join->on('days.id','=','operational_time.day_id');
                    $join->where('outlet_id',$outlet);
                })
                ->get();

            foreach($data as $d){
                if($d->is_24 == null && $d->is_close == null && $d->is_custom_time == null){
                    $d->set_status = 'Not Set';
                    $d->start = '00:00';
                    $d->end = '00:00';
                }else{
                    $d->time_slot = json_decode($d->time_slot);
                    if($d->is_24 == true){
                        $d->start = '00:00';
                        $d->end = '23:59';
                    }elseif($d->is_close == true){
                        $d->start = '00:00';
                        $d->end = '00:00';
                    }elseif($d->is_custom_time == true){
                        if($d->time_slot[0]){
                            $d->start = $d->time_slot[0]->start;
                            $d->end = $d->time_slot[(count($d->time_slot)-1)]->end;
                        }else{
                            $d->start = '00:00';
                            $d->end = '00:00';
                        }
                    }
                    $d->set_status = 'Already Set';
                }
            }

            return $this->json200($data);
        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    public function setOperationalTime(Request $request, JagomartRequest $valid){
        $check = $valid->validateTimeOperational($request);

        if(is_string($check)){
            return $this->json422($check);
        }

        DB::beginTransaction();

        try{
            $time = OperationalTime::firstOrNew([
                'day_id'=>$request->input('day_id'),
                'outlet_id' => $this->outlet_id
            ]);

            $time->fill($request->except(['day_id']));

            $time->outlet_id = $this->outlet_id;

            $time->save();

            DB::commit();

            return $this->json200($time);
        }catch (\Exception $e){
            DB::rollBack();

            return $this->json200($e->getMessage());
        }

    }

    public function getPromoOutlet(Request $request) {
        $offset = $request->input('offset') ?? 0;
        $limit = $request->input('limit') ?? 5;

        Outlet::withCount('promo');
    }

}

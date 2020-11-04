<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/1/20, 8:25 AM
 *
 */

namespace App\Http\Controllers\Driver\Ride;

use App\Http\Controllers\Controller;
use App\Models\Driver\UserVehicle;
use App\Traits\FileUpload;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MotorController extends Controller
{
    use JsonResponse, FileUpload;

    public $user_id;

    public function __construct()
    {
    	$this->user_id = request()->user_id;
    }

    public function getList()
    {
        return $this->json200($this->dataMotorResponse());
    }

    public function addMotor(Request $request)
    {
        $this->validate($request, [
            'plat_number' => 'required|string|max:25',
            'brand_id' => 'required|exists:App\Models\Master\VehicleBrand,id',
            'model_id' => 'required|exists:App\Models\Master\VehicleModel,id',
            'year' => 'required|date_format:Y',
            'stnk_valid_until' => 'required|date_format:d/m/Y',
            'stnk_image' => 'required|image'
        ]);

        $motor = new UserVehicle();

        $data = $request->all();
        $data['user_id'] = $this->user_id;
        $data['stnk_valid_until'] = Carbon::createFromFormat('d/m/Y',$data['stnk_valid_until']);

        if($request->hasFile('stnk_image')) {
            $data['stnk_image'] = $this->upload($request->file('stnk_image'),'driver/motor/stnk/');
        }

        try{
            $motor->fill($data);

            $motor->save();

            return $this->json200($this->dataMotorResponse($motor->id));

        }catch (\Exception $e){
            return $this->json500($e->getMessage());
        }
    }

    public function updateMotor(Request $request) {
        $this->validate($request, [
            'id' => 'required|numeric',
            'plat_number' => 'required|string|max:25',
            'brand_id' => 'required|exists:App\Models\Master\VehicleBrand,id',
            'model_id' => 'required|exists:App\Models\Master\VehicleModel,id',
            'year' => 'required|date_format:Y',
            'stnk_valid_until' => 'required|date_format:d/m/Y',
            'stnk_image' => 'nullable|image'
        ]);

        $motor = UserVehicle::find($request->input('id'));

        if(!$motor) 
            return $this->json500("Kendaraan tidak ditemukan.");

        $data = $request->all();
        $data['user_id'] = $this->user_id;
        $data['stnk_valid_until'] = Carbon::createFromFormat('d/m/Y',$data['stnk_valid_until']);

        if($request->hasFile('stnk_image')) {
            $data['stnk_image'] = $this->upload($request->file('stnk_image'),'driver/motor/stnk/');
        }

        try{
            $motor->fill($data);
            $motor->save();
            return $this->json200($this->dataMotorResponse($motor->id));

        }catch (\Exception $e){
            return $this->json500($e->getMessage());
        }
    }

    public function deleteMotor(Request $request) {
        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $motor = UserVehicle::find($request->input('id'));

        if(!$motor) 
            return $this->json500("Kendaraan tidak ditemukan.");

        // TODO: DELETE STNK IMAGE

        $motor->delete();
        return $this->json200("Kendaraan berhasil dihapus.");        
    }

    public function useMotor(Request $request) {
        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $motor = UserVehicle::find($request->input('id'));

        if(!$motor) 
            return $this->json500("Kendaraan tidak ditemukan.");

        try {
            UserVehicle::where('user_id', $this->user_id)->update(['is_used' => false]);
            $motor->is_used = true;
            $motor->save();

            return $this->json200("Kendaraan berhasil digunakan.");
        } catch (\Exception $e) {
            return $this->json500($e->getMessage());
        }
    }

    private function dataMotorResponse($vehicle_id=null)
    {
        $select = [
            'user_vehicles.id',
            'user_vehicles.plat_number',
            'vehicle_brand.name as brand',
            'vehicle_model.name as model',
            'is_used',
            DB::raw('CASE WHEN verified_at = NULL THEN true ELSE false END as verification_status')
        ];

        $vehicles = UserVehicle::select($select)
            ->leftJoin('master.vehicle_brand', 'vehicle_brand.id', '=', 'user_vehicles.brand_id')
            ->leftJoin('master.vehicle_model', 'vehicle_model.id', '=', 'user_vehicles.model_id')
            ->where('user_id', $this->user_id);

        if($vehicle_id)
        {
            return $vehicles->find($vehicle_id);
        }else{
            return $vehicles->get();
        }
    }
}

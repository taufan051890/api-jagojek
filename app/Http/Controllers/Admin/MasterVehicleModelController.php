<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/29/20, 10:15 AM
 *
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Master\VehicleModel;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class MasterVehicleModelController extends Controller
{
    use JsonResponse;

    public function get(Request $request){
        $req = $request->only(['offset','limit','search']);

        $offset = $req['offset'] ?? 0;
        $limit = $req['limit'] ?? 0;
        $search = $req['search'] ?? '';

        $data = VehicleModel::select('id','name');

        if($limit>0){
            $data->offset($offset);
            $data->limit($limit);
        }

        if($search){
            $data->where('name','~*',$search);
        }

        $data->orderBy('id','asc');

        return $this->json200($data->get());

    }

    public function create(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'brand_id' => 'required|exists:App\Models\Master\VehicleBrand,id'
        ]);

        $vehicle_model = new VehicleModel();
        $vehicle_model->name = $request->input('name');
        $vehicle_model->brand_id = $request->input('brand_id');
        $vehicle_model->save();
        $vehicle_model->refresh();

        return $this->json200($vehicle_model);
    }

    public function update(Request $request){
        $this->validate($request,[
            'edited_id' => 'required|exists:App\Models\Master\VehicleModel,id',
            'name' => 'required',
            'brand_id' => 'required|exists:App\Models\Master\VehicleBrand,id'
        ]);

        $vehicle_model = VehicleModel::find($request->input('edited_id'));
        $vehicle_model->name = $request->input('name');
        $vehicle_model->brand_id = $request->input('brand_id');
        $vehicle_model->save();
        $vehicle_model->refresh();

        return $this->json200($vehicle_model);
    }

    public function delete(Request $request){

        $this->validate($request,[
            'deleted_id' => 'required|exists:App\Models\Master\VehicleModel,id'
        ]);

        try{
            $vehicle_model = VehicleModel::find($request->deleted_id);
            $vehicle_model->delete();

            return $this->json200('Delete Successful.');

        }catch (\Exception $e){

            return $this->json200('Cannot Delete Data. This data already used.');

        }

    }
}

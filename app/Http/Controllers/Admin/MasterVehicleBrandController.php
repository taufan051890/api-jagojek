<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/29/20, 1:24 AM
 *
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Master\VehicleBrand;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class MasterVehicleBrandController extends Controller
{
    use JsonResponse;

    public function getBrand(Request $request){
        $req = $request->only(['offset','limit','search']);

        $offset = $req['offset'] ?? 0;
        $limit = $req['limit'] ?? 0;
        $search = $req['search'] ?? '';

        $data = VehicleBrand::select('id','name');

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

    public function createBrand(Request $request){

        $this->validate($request,[
            'name' => 'required|unique:master.vehicle_brand,name'
        ]);

        $brand = new VehicleBrand();
        $brand->name = $request->input('name');
        $brand->save();
        $brand->refresh();

        return $this->json200($brand);
    }

    public function updateBrand(Request $request){
        $this->validate($request,[
            'id' => 'required|exists:App\Models\Master\VehicleBrand,id',
            'name' => 'required|unique:master.vehicle_brand,name,'.request()->id
        ]);

        $brand = VehicleBrand::find($request->id);
        $brand->name = $request->input('name');
        $brand->save();
        $brand->refresh();

        return $this->json200($brand);
    }

    public function deleteBrand(Request $request){
        $this->validate($request,[
            'id' => 'required|exists:App\Models\Master\VehicleBrand,id'
        ]);

        try{
            $brand = VehicleBrand::find($request->id);
            $brand->delete();

            return $this->json200('Delete Successful.');

        }catch (\Exception $e){

            return $this->json200('Cannot Delete Data. This data already used.');

        }
    }

}

<?php
namespace App\Http\Controllers\Jagomart;

use App\Http\Controllers\Controller;
use App\Models\Jagomart\UserToken;
use App\Models\Jagomart\Outlet;
use App\Models\Jagomart\User;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestController extends Controller
{
    use JsonResponse;

    public function createNewUser(Request $request){
        DB::beginTransaction();

        try{
            $user = new User();
            $user->phone_number = $request->input('phone_number');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->gender = $request->input('gender');
            $user->status = true;
            $user->phone_verified_at = Carbon::now();
            $user->save();

            $outlet = new Outlet();
            $outlet->user_id = $user->id;
            $outlet->name = $request->input('outlet_name');
            $outlet->address = $request->input('outlet_address');
            $outlet->latitude = $request->input('outlet_latitude');
            $outlet->longitude = $request->input('outlet_longitude');
            $outlet->bank_owner = $request->input('bank_owner');
            $outlet->bank_number = $request->input('bank_number');
            $outlet->bank_name = $request->input('bank_name');
            $outlet->bank_email_report = $request->input('bank_email_report');
            $outlet->banner = 'https://cdn.assets.jagojek.id/images/placeholder/outlet.jpg';

            $outlet->geo_location =
                'POINT('.$request->outlet_longitude.' '.$request->outlet_latitude.')';

            $outlet->save();

            $token = new UserToken();
            $token->user_id = $user->id;
            $token->login_at = Carbon::now();
            $token->token = Str::random(128);
            $token->save();

            DB::commit();

            return $this->json200('Test User Created Successfully with Bearer Token '. $token->token);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->json500($e->getMessage());
        }


    }

}

<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/31/20, 9:23 PM
 *
 */

namespace App\Http\Middleware;

use App\Models\Driver\UserToken;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;


class JagorideAuth
{
    use JsonResponse;

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if($token!=null){

            $check = UserToken::where('token',$token)
                ->whereNull('logout_at')
                ->first();

            if(!$check){
                return $this->json500('Invalid Token');
            }

            try{
                $check->ip_address = $request->ip();
                $check->latest_activity_at = Carbon::now();
                $check->save();
            }catch (\Exception $e){
                return $this->json500($e->getMessage());
            }


        }else{
            return $this->json401();
        }

        //Get Data User & Outlet
        $user_outlet = DB::table('driver.user')
            ->select('user.id as user_id')
            ->where('user.id',$check->user_id)
            ->where('type','ride')
            ->first();

        if(!$user_outlet){
            return $this->json401();
        }

        $request->user_id = $user_outlet->user_id;

        return $next($request);
    }
}

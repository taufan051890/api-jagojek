<?php
/**
 * Copyright (c) 2020.
 */

namespace App\Http\Middleware;

use App\Models\Customer\UserToken;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class CustomerMiddleware
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
                return $this->json200('Invalid Token');
            }

            try{
                $check->ip_address = $request->ip();
                $check->latest_activity_at = Carbon::now();
                $check->save();
            }catch (\Exception $e){
                return $this->json500($e->getMessage());
            }

            if(!$check){
                return $this->json200('Invalid Token');
            }

        }else{
            return $this->json500('Unauthorized Action');
        }

        //Get Data User & Outlet
        $user_outlet = DB::table('customer.users as user')
            ->select('user.id as user_id','phone_number')
            ->where('user.id',$check->user_id)
            ->first();

        $request->user = $user_outlet;

        return $next($request);
    }
}

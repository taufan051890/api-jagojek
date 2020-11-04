<?php

namespace App\Services\Auth;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService implements AuthServiceContract {

    public function generateToken($auth_id,$device=null,$os=null)
    {
        $token = Str::random(128);

        DB::beginTransaction();

        try{
            DB::table('public.AuthToken')
                ->insert([
                    'auth_id' => $auth_id,
                    'token' => $token,
                    'device' => $device,
                    'os' => $os,
                    'login_at' => \Carbon\Carbon::now()
                ]);

            DB::commit();

            return $token;

        }catch (\Exception $e){
            DB::rollBack();

            return $e->getMessage();
        }
    }

    public function validateToken($token)
    {
        $check = DB::table('public.AuthToken')
            ->where('token',$token)
            ->whereNull('logout_at')
            ->first();

        if($check){
            return true;
        }else{
            return false;
        }
    }

    public function expireToken($token)
    {
        DB::beginTransaction();

        try{

            DB::table('public.AuthToken')
                ->where('token',$token)
                ->update([
                    'logout_at' => \Carbon\Carbon::now()
                ]);

            DB::commit();
            return false;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public function getUserId($token)
    {
        $auth_token_db = DB::table('public.AuthToken as auth_token')
            ->select('fidUser as id')
            ->where('auth_token.token',$token)
            ->whereNull('auth_token.logout_at')
            ->join('public.Auth as auth','auth.idAuth',
                '=','auth_token.auth_id')
            ->first();

        if($auth_token_db){
            return $auth_token_db->id;
        }else{
            return 'User ID not Found';
        }
    }

    public function getPartnerId($token){
        $auth_token_db = DB::table('public.AuthToken auth_token')
            ->select('fidPartner as id')
            ->where('auth_token.token',$token)
            ->whereNull('auth_token.logout_at')
            ->join('public.Auth auth','auth.idAuth','=','')
            ->first();

        if($auth_token_db){
            return $auth_token_db->id;
        }else{
            return 'Partner ID not Found';
        }
    }
}

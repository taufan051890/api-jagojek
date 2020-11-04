<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use App\Traits\TwilioHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use TwilioHelper, JsonResponse;

    protected $tb_user = 'humanCapital.hcUser';

    protected $tb_token = 'humanCapital.hcUserToken';

    public function sendOTP(Request $request, $action){

        $phone = $request->input('phone');

        if(!is_numeric($phone)){
            return $this->json422('Nomor telepon harus berbentuk angka.'.$phone);
        }

        $data = DB::table($this->tb_user)
            ->where('phone_number','=',$phone)
            ->whereNotNull('phone_verified_at')
            ->count();

        if($data>0){
            $is_available = true;
        }else{
            $is_available = false;
        }

        if($action=='login'){
            if(!$is_available){

                return $this->json500('Nomor telepon belum terdaftar.');

            }else{
                $verify = $this->startVerification($phone);
                if(!is_string($verify)){

                    return $this->json200('OTP telah terkirim');

                }else{

                    return $this->json500('500 : ' . $verify);

                }
            }
        }else if($action=='register'){
            if($is_available){
                return $this->json500('Nomor telepon sudah terdaftar');
            }else{
                $verify = $this->startVerification($phone);

                if(!is_string($verify)){

                    return $this->json200('OTP telah terkirim');

                }else{

                    return $this->json500('500 : ' . $verify);

                }
            }

        }else{

            return $this->json500('Action Not Found');

        }
    }


    public function login(Request $request){
        if($request->input('phone')){
            $verify = $this->verifyAuthentication($request->input('phone'),$request->input('otp'));

            if(!is_string($verify)){
                if($verify->status === 'approved'){
                    $user = DB::table($this->tb_user)
                        ->where('phone_number',$request->input('phone'))
                        ->first();

                    $count_token = DB::table($this->tb_token)
                        ->where('user_id',$user->id)
                        ->count();

                    if($count_token <= 0){
                        $first_login = true;
                    }else{
                        $first_login = false;
                    }

                    $user->token = $this->generateToken($user->id, $request->userAgent(),$request->ip());
                    $user->first_login = $first_login;

                    return $this->json200($user);
                }else{
                    return $this->json500('Gagal Verifikasi, Silahkan Cek Nomer Telepon dan Kode OTP.');
                }
            }else{
                return $this->json500('Gagal Verifikasi, Silahkan Cek Nomer Telepon dan Kode OTP.');
            }
        }else{
            return $this->json500('Nomor Telepon diperlukan.');
        }
    }


    /**
     *
     * Generate User Token
     *
     * @param $user_id
     * @param null $device
     * @param null $ip_address
     * @return string
     */
    protected function generateToken($user_id, $device=null, $ip_address = null){
        $token = Str::random(128);

        DB::beginTransaction();

        try{
            DB::table($this->tb_token)
                ->insert([
                    'user_id' => $user_id,
                    'token' => $token,
                    'device' => $device,
                    'ip_address' => $ip_address,
                    'login_at' => \Carbon\Carbon::now()
                ]);

            DB::commit();

            return $token;

        }catch (\Exception $e){
            DB::rollBack();

            return $e->getMessage();
        }
    }

    public function logout(Request $request){
        $token = $request->bearerToken();

        if($this->expireToken($token)){
            return $this->json200('Logout Successful.');
        }else{
            return $this->json500('Logout Unsuccessful.');
        }

    }

    /**
     * Check Token Valid Or Not
     *
     * @param $token
     * @return bool
     */

    private function validateToken($token){
        $check = DB::table($this->tb_token)
            ->where('token',$token)
            ->whereNull('logout_at')
            ->first();

        if($check){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Expiring User Token
     *
     * @param $token
     * @return bool
     */
    private function expireToken($token){
        DB::beginTransaction();

        try{

            DB::table($this->tb_token)
                ->where('token',$token)
                ->update([
                    'logout_at' => \Carbon\Carbon::now()
                ]);

            DB::commit();

            return true;

        }catch (\Exception $e){
            DB::rollBack();

            return false;
        }
    }

    private function getUserData($token){

    }



}

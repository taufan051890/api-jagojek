<?php
namespace App\Http\Controllers\Driver;

use App\Http\Controllers\AuthController as Controller;
use App\Models\Driver\User;
use App\Traits\JsonResponse;
use App\Traits\TwilioHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use JsonResponse, TwilioHelper;

    public function __construct()
    {
        $this->tb_user = 'driver.user';
        $this->tb_token = 'driver.user_token';
    }

    public function registerJagoride(Request $request)
    {
        $this->validate($request,[
            'phone_number' => 'required|numeric',
            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'gender' => 'required|in:L,P',
            'otp' => 'required|numeric|digits:6',
            'cities_id' => 'required|numeric|exists:App\Models\Master\City,id'
        ]);

        $verify = $this->verifyAuthentication($request->input('phone_number'),$request->input('otp'));

        if(!is_string($verify))
        {
            if($verify->status === 'approved'){
                try{
                    $user = new User();

                    $data = $request->all();
                    $data['status'] = true;
                    $data['type'] = 'ride';
                    $data['phone_verified_at'] = Carbon::now();

                    $user->fill($data);
                    $user->save();

                    $user->token = $this->generateToken($user->id,$request->userAgent(),$request->ip());

                    return $this->json200($user);

                }catch (\Exception $e){
                    return $this->json500('Nomor Telepon Sudah Digunakan.');
                }
            }else{
                return $this->json500('Check OTP atau Nomer Telepon');
            }

        }else{
            return $this->json500('Check OTP atau Nomer Telepon.');
        }

    }

    public function getProfile() {
        $user_id = request()->user_id;

        return $this->json200(User::find($user_id));
    }

    public function sendOTPUpdateNumber(Request $request)
    {

        $this->validate($request, [
            'phone' => 'required|numeric|unique:App\Models\Driver\User,phone_number'
        ]);

        $verify = $this->startVerification($request->phone);
        if(!is_string($verify)){
            return $this->json200('OTP Telah Dikirim Ke Nomer Baru');
        }else{
            return $this->json500('OTP Gagal Dikirim, Silahkan Periksa Kembali Nomor Anda');
        }

    }

    public function changeNumber(Request $request)
    {
        $user_id = request()->user_id;

        $this->validate($request,[
            'old_phone_number' => 'required|numeric|exists:App\Models\Driver\User,phone_number',
            'new_phone_number' => 'required|numeric|unique:App\Models\Driver\User,phone_number',
            'otp' => 'required|digits:6'
        ]);

        $new_phone = $request->input('new_phone_number');
        $otp = $request->input('otp');

        $verify = $this->verifyAuthentication($new_phone,$otp);

        if(!is_string($verify)){
            if($verify->status === 'approved') {
                $user = User::find($user_id);

                $user->phone_number = $new_phone;

                $user->save();

                return $this->json200('Success Update Phone Number');
            }else{
                return $this->json200('OTP tidak valid.');
            }

        }else{
            return $this->json200('OTP Salah.');
        }
    }

}

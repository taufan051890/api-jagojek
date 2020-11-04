<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\AuthController as Controller;
use App\Models\Customer\Customer;
use App\Traits\JsonResponse;
use App\Traits\TwilioHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use JsonResponse, TwilioHelper;

    public function __construct()
    {
        $this->tb_user = 'customer.users';
        $this->tb_token = 'customer.user_token';
    }

    public function register(Request $request) {
        $this->validate($request,[
            'full_name' => 'required|max:50',
            'birth_date' => 'required|date_format:d/m/Y',
            'gender' => 'required|in:L,P',
            'phone_number' => 'required|numeric',
            'otp' => 'required|digits:6'
        ]);

        $verify = $this->verifyAuthentication($request->input('phone_number'),$request->input('otp'));

        if(!is_string($verify)){

            try {
                if($verify->status === 'approved') {
                    $customer = new Customer();
                    $customer->phone_number = $request->input('phone_number');
                    $customer->name = $request->input('full_name');
                    $customer->gender = $request->input('gender');
                    $customer->birth_date = Carbon::createFromFormat('d/m/Y', $request->input('birth_date'));
                    $customer->phone_verified_at = Carbon::now();

                    if ($customer->save()) {
                        $customer->refresh();
                        $customer->token = $this->generateToken($customer->id, $request->userAgent(), $request->ip());

                        return $this->json200($customer);
                    } else {
                        return $this->json500('Registration Failure.');
                    }
                }else{
                    return $this->json500('Check OTP or Phone Number');
                }
            }catch (\Exception $e){
                return $this->json500('500 :' . $e->getMessage());
            }

        }else{
            return $this->json500('500 :' . $verify);
        }
    }
}

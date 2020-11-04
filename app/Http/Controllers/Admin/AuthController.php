<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use App\Traits\TwilioHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use TwilioHelper, JsonResponse;

    public function login(Request $request){
        $user = DB::table('employee.users')
            ->where('email',$request->input('email'))
            ->first();

        if($user){
            if(Hash::check($request->input('password'),$user->password)){
                $user->token = $this->generateToken($user->id, $request->userAgent());

                return $this->json200($user);
            }else{
                return $this->json500('Invalid Username or Password');
            }
        }else{
            return $this->json500('Invalid Username or Password');
        }

    }

    private function generateToken($user_id, $device=null, $os=null){

        $token = Str::random(128);

        DB::beginTransaction();

        try{
            DB::table('employee.user_token')
                ->insert([
                    'user_id' => $user_id,
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

}

<?php

namespace App\Http\Controllers\Frontend;
use App\Services\Auth\AuthServiceContract;
use App\Traits\JsonResponse;
use App\Traits\TwilioHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use JsonResponse, TwilioHelper;

    function registerUser(Request $request){
        $messages = [
            'required' => ':attribute wajib diisi'
        ];

        $validator = Validator::make($request->all(),[
            'Name' => 'required|string|max:20',
            'PhoneNumber' => 'required|string|max:15',
            'JenisKelamin' => 'required|in:L,P',
            'BirthDate' => 'required|date'
        ],$messages);

        if($validator->fails()){
            return $this->json422(implode($validator->getMessageBag()->all(),','));
        }

        $check_available = DB::table('humanCapital.hcUser')
            ->where('PhoneNumber',$request->input('PhoneNumber'))
            ->first();

        DB::beginTransaction();

        if($check_available){
            try{
                if($check_available->PhoneVerifiedAt!=null){
                    return $this->json422('Nomor Telepon Sudah Digunakan.');
                }else{
                    $delete_auth = DB::table('public.Auth')
                        ->where('fidUser',$check_available->idUser)
                        ->delete();

                    $delete_user = DB::table('humanCapital.hcUser')
                        ->where('idUser',$check_available->idUser)
                        ->delete();

                }
            }catch (\Exception $e){
                return $this->json500($e->getMessage());
            }

        }

        $data = $request->all();
        $data['JoinDate'] = \Carbon\Carbon::now();

        try{
            $hcuser = DB::table('humanCapital.hcUser')->insertGetId($data,'idUser');

            if($hcuser){
                if(!$this->startVerification($request->PhoneNumber)){
                    return $this->json500('SMS Gagal Terkirim.');
                }

                $latest_username = DB::table('public.Auth')
                    ->select('Username')
                    ->orderBy('idAuth','DESC')
                    ->first();

                $auth_username = '';

                if(!$latest_username){
                    $auth_username = 'USR-1';
                }else{
                    $current_username = explode('-',$latest_username->Username);
                    $auth_username = $current_username[0].'-'.($current_username[1]+1);
                }

                $public_auth = DB::table('public.Auth')->insert([
                   'Username' => $auth_username,
                   'fidUser' => $hcuser
                ]);
            }

            DB::commit();

            return $this->json200('SMS Sudah Terkirim');

        }catch (\Exception $e){
            DB::rollBack();

            return $this->json500($e->getMessage());
        }

    }

    function verifyOTPRegister(Request $request, AuthServiceContract $authServiceContract){
        $validator = Validator::make($request->all(),[
            'phone' => 'required',
            'code' => 'required'
        ]);

        if($validator->fails()){
            return $this->json422(implode($validator->getMessageBag()->all(),','));
        }

        //Start Verification
        $verify = $this->verifyAuthentication($request->input('phone'),$request->input('code'));

        if(!is_string($verify)){
            if($verify->status === 'approved'){

                try{
                    $user = DB::table('humanCapital.hcUser')
                        ->where('PhoneNumber',$request->input('phone'));

                    $user->update([
                        'PhoneVerifiedAt' => \Carbon\Carbon::now()
                    ]);

                    $auth_id = $user->select('idAuth as id')
                        ->join('public.Auth','public.Auth.fidUser',
                            '=','humanCapital.hcUser.idUser')
                        ->first();

                    $token = $authServiceContract->generateToken($auth_id->id);

                    return $this->json200([
                        'token' => $token
                    ]);

                }catch (\Exception $e){
                    return $this->json500($e->getMessage());
                }
            }else{
                return $this->json500('Invalid OTP.');
            }
        }else{
            return $this->json500('Invalid OTP Number');
        }

    }

    function sendOTPMessage($phone){

        if(!is_numeric($phone)){
            return $this->json422('Nomor Telepon Harus Angka.');
        }

        $data = DB::table('humanCapital.hcUser')
            ->where('PhoneNumber','=',$phone)
            ->whereNotNull('PhoneVerifiedAt')
            ->count();

        if($data==0){
            return $this->json422('Nomor Telepon Belum Terdaftar.');
        }else {

            $send_message = $this->startVerification($phone);

            if($send_message){
                return $this->json200('SMS Telah Dikirimkan');
            }else{
                return $this->json200('SMS Gagal Dikirimkan, Silahkan Coba Lagi');
            }
        }
    }

    function verifyLoginUser(Request $request, AuthServiceContract $authServiceContract){
        $validator = Validator::make($request->all(),[
            'phone' => 'required',
            'code' => 'required'
        ]);

        if($validator->fails()){
            return $this->json422(implode($validator->getMessageBag()->all(),','));
        }

        //Start Verification
        $verify = $this->verifyAuthentication($request->input('phone'),$request->input('code'));

        if(is_ob($verify)){
            if($verify->status === 'approved') {

                $user = DB::table('humanCapital.hcUser')
                    ->where('PhoneNumber',$request->input('phone'));

                $auth_id = $user->select('idAuth as id')
                    ->join('public.Auth','public.Auth.fidUser',
                        '=','humanCapital.hcUser.idUser')
                    ->first();

                $token = $authServiceContract->generateToken($auth_id->id);

                return $this->json200([
                    'token' => $token
                ]);
            }else{
                return $this->json500('Invalid OTP');
            }
        }else{
            return $this->json500('Invalid OTP');
        }
    }

    function checkPhoneNumber($phone){

        if(!is_numeric($phone)){
            return $this->json422('Phone must be number.');
        }

        $data = DB::table('humanCapital.hcUser')
            ->where('PhoneNumber','=',$phone)
            ->whereNotNull('PhoneVerifiedAt')
            ->count();

        if($data>0){
            return $this->json422('Nomor Telepon Sudah Digunakan.');
        }

        return $this->json200('Nomor Telepon Dapat Digunakan.');

    }


}

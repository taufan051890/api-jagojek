<?php
namespace App\Http\Controllers\Jagomart;

use App\Http\Controllers\Controller;
use App\Models\Jagomart\User;
use App\Services\Jagomart\JagomartRequest;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use JsonResponse;

    private $user_id;

    public function __construct()
    {
        $this->user_id = request()->user->user_id;
    }

    public function setPin(Request $request,JagomartRequest $valid){
        $user = User::find($this->user_id);

        if($user->pin==null){
            $check = $valid->validatePin($request,true);
        }else{
            $check = $valid->validatePin($request);
        }

        if(is_string($check)){
            return $this->json422($check);
        }

        if($user->pin==null){
            $user->pin = app('hash')->make($request->input('new_pin'));
            $user->save();
        }else{
            if(Hash::check($request->input('old_pin'),$user->pin)){
                $user->pin = app('hash')->make($request->input('new_pin'));
                $user->save();
            }else{
                return $this->json422('Invalid Old Pin');
            }
        }



        return $this->json200('Success');
    }

}

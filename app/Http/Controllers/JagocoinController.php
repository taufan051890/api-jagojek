<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jagocoin\Account;
use App\Models\Jagocoin\Balance;
use Illuminate\Http\Request;

class JagocoinController extends Controller
{

    protected function getName()
    {
        return 'Anonymous';
    }

    protected function getAccount($phone)
    {
        $account = Account::where('phone',$phone)->firstOrNew();

        if(!$account->id){
            $account->phone = $phone;
            $account->owner_name = $this->getName();
            $account->save();
        }

        return $account->id;
    }

    protected function getBalance($account_id)
    {
        $balance = Balance::where('account_id',$account_id)
            ->orderBy('id','DESC')
            ->first();

        if ($balance) {
            return $balance->balance;
        } else {
            return 0;
        }
    }
}

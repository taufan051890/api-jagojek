<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\JagocoinController as Controller;

use App\Models\Customer\Customer;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    use JsonResponse;

    private $customer_id;

    public function __construct()
    {
        $this->customer_id = request()->user->user_id;
    }

    protected function getName()
    {
        return Customer::find($this->customer_id)->name;
    }

    public function getMyJagocoin()
    {
        $account_id = $this->getAccount(request()->user->phone_number);
        $balance = $this->getBalance($account_id);

        return $this->json200($balance);
    }

}

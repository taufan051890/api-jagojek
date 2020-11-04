<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;

class CustomerController extends Controller
{
    use JsonResponse;

    public function getUserTable(Request $request){
        $page = $request->get('page');
        $per_page = $request->get('itemsPerPage');

        $data['total'] = Customer::count();
        $data['items'] = Customer::skip($page)
            ->take($per_page)
            ->get();

        return $this->json200($data);
    }

}

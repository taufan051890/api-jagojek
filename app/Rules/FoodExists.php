<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/6/20, 11:37 PM
 *
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Jagofood\Food;

class FoodExists implements Rule
{
    private $outlet_id;

    public function __construct($outlet_id)
    {
    	$this->outlet_id = $outlet_id;
    }


    public function passes($attribute, $value)
    {
        $outlet_id = $this->outlet_id;
        $check = Food::whereHas('category',function($query) use ($outlet_id){
            $query->where('outlet_id',$outlet_id);
        })->where('id',$value)->count();

        if($check>0){
            return true;
        }else{
            return false;
        }
    }

    public function message()
    {
        return ':attribute item ada';
    }
}

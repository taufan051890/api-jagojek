<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 5:32 PM
 *
 */

namespace App\Models\Transaction;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FoodOrder extends Model
{
    protected $table = 'transaction.food_order';

    protected $casts = [
        'total_food_price' => 'float',
        'ongkir_price' => 'float',
        'total_discount' => 'float',
        'final_price' => 'float',
        'order_at' => 'datetime:Y-m-d H:i:s',
        'success_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function details(){
        return $this->hasMany('App\Models\Transaction\FoodOrderDetail','food_order_id');
    }

    public function outlet() {
        return $this->belongsTo('App\Models\Jagofood\Outlet', 'outlet_id', 'id');
    }

    public function driver() {
        return $this->belongsTo('App\Models\Driver\User', 'driver_id', 'id');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Customer\Customer', 'customer_id', 'id');
    }

}

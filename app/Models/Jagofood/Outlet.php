<?php

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model{
    protected $table = 'jagofood.outlet';

    protected $casts = [
        'rating' => 'decimal:1'
    ];

    public function loves(){
        return $this->hasMany(
            'App\Models\Jagofood\Love',
            'outlet_id',
            'id')->where('loves.status',true);
    }

    public function order()
    {
        return $this->hasMany('App\Models\Transaction\FoodOrder','outlet_id','id');
    }

    public function promo()
    {
        return $this->hasMany('App\Models\Jagofood\Promo'
            ,'outlet_id',
            'id');
    }

    public function category()
    {
        return $this->hasMany('App\Models\Jagofood\Category','outlet_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction\FoodOrder', 'outlet_id');
    }
}

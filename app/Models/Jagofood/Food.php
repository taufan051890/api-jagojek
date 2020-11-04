<?php

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Food extends Model{

    protected $table = 'jagofood.food';

    protected $casts = [
        'price' => 'float'
    ];

    public function category(){
        return $this->belongsTo('App\Models\Jagofood\Category',
            'category_id','id');
    }

    public function promo()
    {
        return $this->hasMany('App\Models\Jagofood\Promo'
            ,'food_id',
            'id');
    }
}

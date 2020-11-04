<?php

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model{

    protected $table = 'jagofood.promo';

    protected $casts = [
        'food_price_after_promo' => 'float',
        'minimum_order' => 'float',
        'discount' => 'float',
        'item_price_before_promo' => 'float',
        'max_discount' => 'float'
    ];
}

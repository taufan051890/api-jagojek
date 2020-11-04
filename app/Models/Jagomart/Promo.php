<?php

namespace App\Models\Jagomart;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model{

    protected $table = 'jagomart.promo';

    protected $casts = [
        'item_price_after_promo' => 'float',
        'minimum_order' => 'float',
        'discount' => 'float',
        'item_price_before_promo' => 'float'
    ];
}

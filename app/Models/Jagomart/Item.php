<?php

namespace App\Models\Jagomart;

use Illuminate\Database\Eloquent\Model;

class Item extends Model{
    protected $table = 'jagomart.item';

    protected $casts = [
        'price' => 'float'
    ];
}

<?php
/**
 * Copyright (c) 2020.
 */

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $table = 'jagofood.rates';

    protected $fillable = ['outlet_id','customer_id','rate'];

    protected $casts = [
        'rate' => 'decimal:1'
    ];
}

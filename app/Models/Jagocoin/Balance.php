<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 10:57 PM
 *
 */

namespace App\Models\Jagocoin;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'jagocoin.balance';

    protected $casts = [
        'balance' => 'float'
    ];

    protected $fillable = [

    ];
}

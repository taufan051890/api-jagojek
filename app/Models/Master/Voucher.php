<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 11:19 AM
 *
 */

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'master.vouchers';

    protected $fillable = [
        'feature',
        'type',
        'code',
        'banner',
        'minimum_order',
        'maximum_discount',
        'discount',
        'start_at',
        'expired_at'
    ];

    protected $casts = [
        'minimum_order' => 'float',
        'maximum_discount' => 'float',
        'max_discount' => 'float'
    ];
}

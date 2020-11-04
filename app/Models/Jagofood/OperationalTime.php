<?php
/**
 * Copyright (c) 2020.
 */

namespace App\Models\Jagofood;


use Illuminate\Database\Eloquent\Model;

class OperationalTime extends Model
{
    protected $table = 'jagofood.operational_time';

    protected $fillable = ['day_id','is_24','is_close','is_custom_time','time_slot'];

    protected $casts = [
        'time_slot' => 'array'
    ];
}

<?php
/**
 * Copyright (c) 2020.
 */

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Love extends Model
{
    protected $table = 'jagofood.loves';

    protected $fillable = ['outlet_id','customer_id','status'];
}

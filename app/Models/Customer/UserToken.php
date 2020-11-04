<?php
/**
 * Copyright (c) 2020.
 */

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model{

    protected $table = 'customer.user_token';

    public $timestamps = false;

}

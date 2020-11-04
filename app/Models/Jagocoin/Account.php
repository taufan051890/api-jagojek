<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 10:56 PM
 *
 */

namespace App\Models\Jagocoin;


use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'jagocoin.accounts';

    public function balance()
    {
        return $this->hasMany('App\Models\Jagocoin\Balance',
            'account_id',
            'id');
    }
}

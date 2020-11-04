<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/31/20, 3:07 PM
 *
 */

namespace App\Models\Driver;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'driver.user';

    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'gender',
        'status',
        'type',
        'phone_verified_at',
        'cities_id'
    ];

    protected $hidden = [
        'pin',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime:Y-m-d H:i:s'
    ];

}

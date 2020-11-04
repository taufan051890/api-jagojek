<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/1/20, 8:27 AM
 *
 */

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;


class UserVehicle extends Model
{

    protected $table = 'driver.user_vehicles';

    protected $fillable = ['user_id','plat_number','brand_id','model_id','year','stnk_valid_until','stnk_image'];

}

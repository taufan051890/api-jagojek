<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/29/20, 12:57 AM
 *
 */

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model{

    protected $table = 'master.vehicle_brand';

    protected $fillable = ['id','name'];

}

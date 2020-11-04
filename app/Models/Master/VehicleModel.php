<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/29/20, 10:16 AM
 *
 */

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model {

    protected $table = 'master.vehicle_model';

    protected $fillable = ['id','name'];

}

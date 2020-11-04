<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Day extends Model{
    protected $table = 'master.days';

    protected $fillable = ['id','name'];

    public $timestamps = false;
}

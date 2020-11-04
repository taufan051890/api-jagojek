<?php

namespace App\Models\Jagomart;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    protected $table = 'jagomart.category';

    protected $fillable = ['name','outlet_id'];
}

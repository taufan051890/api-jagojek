<?php

namespace App\Models\Jagofood;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    protected $table = 'jagofood.category';

    protected $fillable = ['name','outlet_id'];

    public function food()
    {
        return $this->hasMany('App\Models\Jagofood\Food','category_id');
    }

}

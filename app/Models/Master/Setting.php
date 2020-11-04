<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model{
    protected $table = 'master.settings';

    protected $fillable = ['key','value'];

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'value' => 'array'
    ];

}

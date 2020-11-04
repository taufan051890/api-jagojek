<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 5:33 PM
 *
 */

namespace App\Models\Transaction;


use Illuminate\Database\Eloquent\Model;

class FoodOrderDetail extends Model
{
    protected $table = 'transaction.food_order_details';

    protected $fillable = [
        'food_order_id',
        'food_id',
        'name',
        'price',
        'qty',
        'total_price',
        'note'
    ];

    protected $casts = [
        'price' => 'float',
        'total_price' => 'float'
    ];

}

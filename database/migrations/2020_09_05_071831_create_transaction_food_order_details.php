<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionFoodOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction.food_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_order_id')->constrained('transaction.food_order');
            $table->foreignId('food_id')->constrained('jagofood.food');
            $table->string('name');
            $table->float('price');
            $table->integer('qty');
            $table->float('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction.food_order_details');
    }
}

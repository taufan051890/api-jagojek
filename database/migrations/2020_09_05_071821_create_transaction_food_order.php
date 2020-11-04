<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionFoodOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction.food_order', function (Blueprint $table) {
            $table->id();

            // User Active
            $table->foreignId('customer_id')->constrained('customer.users');
            $table->foreignId('driver_id')->nullable()->constrained('driver.user');
            $table->foreignId('outlet_id')->constrained('jagofood.outlet');

            //Status
            $table->unsignedTinyInteger('status')->default(10);

            // Time Stamps
            $table->dateTimeTz('order_at')->nullable();
            $table->dateTimeTz('success_at')->nullable();

            // Price
            $table->enum('payment_type',['cash','jagocoin']);
            $table->double('total_food_price');
            $table->string('discount_code')->nullable();
            $table->double('total_discount')->default(0);
            $table->double('ongkir_price');
            $table->double('final_price');

            $table->string('additional_info')->nullable();
            $table->string('cancel_reason')->nullable();

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
        Schema::dropIfExists('transaction.food_order');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.promo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained('jagofood.outlet');
            $table->enum('type', ['food','total','ongkir']);
            $table->foreignId('food_id')->nullable();
            $table->float('food_price_after_promo')->nullable();
            $table->float('minimum_order')->nullable();
            $table->float('discount')->nullable();
            $table->boolean('status')->default(true);
            $table->date('start_at');
            $table->date('expired_at');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jagofood.discount');
    }
}

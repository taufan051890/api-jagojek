<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagomart.promo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained('jagomart.outlet');
            $table->enum('type', ['product','total','ongkir']);
            $table->foreignId('item_id')->nullable();
            $table->float('item_price_after_promo')->nullable();
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
        Schema::dropIfExists('jagomart.promo');
    }
}

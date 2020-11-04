<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverUserVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver.user_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('driver.user');
            $table->string('plat_number');
            $table->foreignId('brand_id')
                ->constrained('master.vehicle_brand');
            $table->foreignId('model_id')
                ->constrained('master.vehicle_model');
            $table->string('year');
            $table->date('stnk_valid_until');
            $table->string('stnk_image');
            $table->boolean('is_used')->default(false);
            $table->timestampTz('verified_at')->nullable();
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
        Schema::dropIfExists('driver.user_vehicles');
    }
}

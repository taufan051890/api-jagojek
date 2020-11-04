<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterZoneSubdistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master.zone_sub_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('master.zone_cities');
            $table->string('sub_district_name');
            $table->string('postal_code')->unique()->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
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
        Schema::dropIfExists('master.zone_districts');
    }
}

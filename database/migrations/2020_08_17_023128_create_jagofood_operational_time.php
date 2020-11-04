<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJagofoodOperationalTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.operational_time', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained('jagofood.outlet');
            $table->integer('day_id');
            $table->boolean('is_24')->default(true);
            $table->boolean('is_close')->default(true);
            $table->boolean('is_custom_time')->default(false);
            $table->jsonb('time_slot')->nullable()->comment('Time Slot');
            $table->unique(['outlet_id','day_id']);
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
        Schema::dropIfExists('jagofood.operational_time');
    }
}

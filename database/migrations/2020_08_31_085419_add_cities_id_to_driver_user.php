<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCitiesIdToDriverUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver.user', function (Blueprint $table) {
            $table->foreignId('cities_id')
                ->nullable()
                ->constrained('master.zone_cities')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver.user', function (Blueprint $table) {
            $table->dropForeign(['cities_id']);
            $table->dropColumn('cities_id');
        });
    }
}

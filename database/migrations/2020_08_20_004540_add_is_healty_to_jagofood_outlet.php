<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsHealtyToJagofoodOutlet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jagofood.outlet', function (Blueprint $table) {
            $table->boolean('is_healthy_menu')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jagofood.outlet', function (Blueprint $table) {
            $table->dropColumn('is_healthy_menu');
        });
    }
}

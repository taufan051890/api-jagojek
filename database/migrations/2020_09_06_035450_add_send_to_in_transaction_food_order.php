<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendToInTransactionFoodOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction.food_order', function (Blueprint $table) {
            $table->string('customer_address')->nullable();
            $table->double('customer_latitude')->nullable();
            $table->double('customer_longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction.food_order', function (Blueprint $table) {
            $table->dropColumn('customer_address');
            $table->dropColumn('customer_latitude');
            $table->dropColumn('customer_longitude');
        });
    }
}

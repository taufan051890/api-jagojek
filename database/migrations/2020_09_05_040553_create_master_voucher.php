<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master.vouchers', function (Blueprint $table) {
            $table->id();
            $table->enum('feature',[
                'ride',
                'car',
                'food',
                'mart'
            ]);
            $table->enum('type',['ongkir','total']);
            $table->string('code')->unique();
            $table->string('banner')->nullable();
            $table->float('minimum_order');
            $table->float('maximum_discount');
            $table->integer('discount');
            $table->date('start_at');
            $table->date('expired_at');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('master.vouchers');
    }
}

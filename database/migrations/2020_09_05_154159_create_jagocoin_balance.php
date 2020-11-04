<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJagocoinBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagocoin.balance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('jagocoin.accounts');
            $table->unsignedTinyInteger('code'); // 20 : In Top Up 30 : Out

            $table->double('mutation');
            $table->double('balance');

            $table->string('transaction_code')->nullable();
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
        Schema::dropIfExists('jagocoin.balance');
    }
}

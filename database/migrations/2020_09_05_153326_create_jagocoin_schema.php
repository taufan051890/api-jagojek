<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJagocoinSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE SCHEMA IF NOT EXISTS jagocoin");

        Schema::create('jagocoin.accounts', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('owner_name')->nullable();
            $table->boolean('status')->default(true);
            $table->string('id_card_photo')->nullable();
            $table->string('holding_id_card_photo')->nullable();
            $table->dateTimeTz('verified_at')->nullable();
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
        Schema::dropIfExists('jagocoin.accounts');
    }
}

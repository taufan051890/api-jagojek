<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserJagofoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.user', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 20)->unique();
            $table->string('name');
            $table->string('email', 250)->unique();
            $table->enum('gender',['L','P'])->default('L');
            $table->boolean('status')->default(false);
            $table->string('pin')->nullable();
            $table->string('avatar')->nullable();
            $table->timestampTz('phone_verified_at')->nullable();
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
        Schema::dropIfExists('jagofood.user_jagofood');
    }
}

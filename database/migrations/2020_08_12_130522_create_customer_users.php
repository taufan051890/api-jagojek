<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer.users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('name');
            $table->enum('gender',['L','P'])->default('L');
            $table->date('birth_date');
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
        Schema::dropIfExists('customer.users');
    }
}

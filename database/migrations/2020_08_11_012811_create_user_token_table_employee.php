<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokenTableEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee.user_token', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('employee.users')
                ->onDelete('cascade');

            $table->string('token');
            $table->string('device')->nullable();
            $table->string('os')->nullable();

            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->timestamp('latest_activity_at')->nullable();
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
        Schema::dropIfExists('employee.user_token');
    }
}

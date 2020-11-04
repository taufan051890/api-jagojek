<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJagofoodUserToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.user_token',function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')
                ->constrained('jagofood.user')
                ->onDelete('cascade');

            $table->string('token');
            $table->ipAddress('ip_address')->nullable();
            $table->string('device')->nullable();

            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->timestamp('latest_activity_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jagofood.user_token');
    }
}

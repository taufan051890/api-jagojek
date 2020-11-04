<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JagomartUserToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagomart.user_token',function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')
                ->constrained('jagomart.user')
                ->onDelete('cascade');

            $table->string('token');
            $table->ipAddress('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->timestampTz('login_at')->nullable();
            $table->timestampTz('logout_at')->nullable();
            $table->timestampTz('latest_activity_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jagomart.user_token');
    }
}

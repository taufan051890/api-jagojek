<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletJagofoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.outlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('jagofood.user');
            $table->string('name');
            $table->text('address');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('bank_owner');
            $table->string('bank_number');
            $table->string('bank_name');
            $table->string('bank_email_report');
            $table->string('banner');
            $table->boolean('is_open')->default(true);
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
        Schema::dropIfExists('jagofood.outlet_jagofood');
    }
}

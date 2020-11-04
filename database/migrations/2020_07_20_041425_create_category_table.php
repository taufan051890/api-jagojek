<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagomart.category', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('outlet_id')
                ->constrained('jagomart.outlet');
            $table->unique(['name','outlet_id']);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('jagomart.category');
    }
}

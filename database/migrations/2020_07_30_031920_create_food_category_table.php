<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained('jagofood.outlet');
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->unique(['outlet_id','name']);
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
        Schema::dropIfExists('jagofood.category');
    }
}

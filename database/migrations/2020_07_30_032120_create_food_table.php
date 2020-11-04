<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagofood.food', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('jagofood.category');
            $table->string('name');
            $table->string('preview')->nullable();
            $table->string('description', 250)->nullable();
            $table->float('price');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('jagofood.food');
    }
}

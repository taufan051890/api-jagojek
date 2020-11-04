<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jagomart.item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('jagomart.category')
                ->onDelete('cascade');
            $table->string('name', 250);
            $table->string('preview')->nullable();
            $table->double('price');
            $table->string('description', 250);
            $table->integer('stock')->default(0);
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
        Schema::dropIfExists('jagomart.item');
    }
}

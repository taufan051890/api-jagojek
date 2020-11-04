<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMasterDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master.days', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name');
            $table->primary('id');
        });

        $days = [
            [
                'id' => 1,
                'name' => 'Senin'
            ],
            [
                'id' => 2,
                'name' => 'Selasa'
            ],
            [
                'id' => 3,
                'name' => 'Rabu'
            ],
            [
                'id' => 4,
                'name' => 'Kamis'
            ],
            [
                'id' => 5,
                'name' => 'Jumat'
            ],
            [
                'id' => 6,
                'name' => 'Sabtu'
            ],
            [
                'id' => 7,
                'name' => 'Minggu'
            ],
        ];

        DB::table('master.days')->insert($days);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master.days');
    }
}

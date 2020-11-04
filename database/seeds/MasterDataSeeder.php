<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Setting;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'general',
                'value' => json_encode([
                    'app-name' => 'Jagojek'
                ])
            ],
            [
                'key' => 'ongkir',
                'value' => json_encode([
                    'minimum-distance' => 1,
                    'minimum-price' => 5000,
                    'add-distance' => 2,
                    'add-price' => 2000
                ])
            ]
        ];

        Setting::insert($data);
    }
}

<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        for($i=0;$i<100;$i++){
            DB::table('customer.users')->insert([
                'phone_number' => '628'.rand(1111111111,9999999999),
                'email' => $faker->email,
                'name' => $faker->name,
                'gender' => $faker->randomElement(['L','P']),
                'birth_date' => $faker->date('Y-m-d'),
		'phone_verified_at' => \Carbon\Carbon::now(),
            ]);
        }
    }
}

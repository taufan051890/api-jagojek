<?php

use Illuminate\Database\Seeder;
use App\Models\Jagofood\User;
use App\Models\Jagofood\Outlet;
use Illuminate\Support\Facades\DB;
use App\Models\Jagofood\Category;
use App\Models\Jagofood\Food;
use App\Models\Jagofood\OperationalTime;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class JagofoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $min_lat = -6.009261;
        $max_lat = -6.073634;

        $min_long = 107.305021;
        $max_long = 107.380685;

        DB::beginTransaction();

        try{
            for($i=0;$i<250;$i++)
            {
                //Creating User
                $user = new User();
                $user->phone_number = '628'.rand(100000000,9999999999);
                $user->email = $faker->email;
                $user->gender = $faker->randomElement(['L','P']);
                if($user->gender == 'L'){
                    $user->name = $faker->firstNameMale . ' ' . $faker->lastName;
                }else{
                    $user->name = $faker->firstNameFemale . ' ' . $faker->lastName;
                }
                $user->status = true;
                $user->phone_verified_at = \Carbon\Carbon::now();

                $user->save();

                //Creating Outlet
                $lat = $faker->randomFloat(6,$min_lat,$max_lat);
                $long = $faker->randomFloat(6,$min_long,$max_long);
                $outlet = new Outlet();
                $outlet->user_id = $user->id;
                $outlet->name = 'Warung '.$user->name;
                $outlet->description = 'Ini Adalah Warung milik '. $user->name;
                $outlet->address = 'Jalan Khayalan '. rand(1,400);
                $outlet->latitude = $lat;
                $outlet->longitude = $long;
                $outlet->bank_owner = $user->name;
                $outlet->bank_name = $faker->randomElement(['Sendiri','Bersama','Syariah']);
                $outlet->bank_number = $faker->bankAccountNumber;
                $outlet->bank_email_report = $user->email;
                $outlet->banner = 'https://cdn.assets.jagojek.id/images/placeholder/outlet.jpg';
                $outlet->is_open = true;
                $outlet->is_healthy_menu = $faker->boolean;
                $outlet->geo_location = DB::raw("ST_GeomFromText(REPLACE('POINT($long $lat)', ',', '.'))");
                $outlet->save();

                $max_category = rand(1,5);
                // Create Category Outlet
                for($a=0;$a<$max_category;$a++){
                    $cat = new Category();
                    $cat->outlet_id = $outlet->id;
                    $cat->name = 'Category '.($a+1);
                    $cat->status = true;
                    $cat->save();

                    $max_food = rand(1,5);

                    // Generate Food
                    for($b=0;$b<$max_food;$b++)
                    {
                        $food = new Food();
                        $food->category_id = $cat->id;
                        $food->name = $faker->randomElement(['Makanan ','Minuman ']) . ($b+1);
                        $food->preview = $faker->imageUrl(320,240,'food');
                        $food->description = 'Ini Adalah '.$food->name;
                        $food->price = rand(10000,30000);
                        $food->status = true;
                        $food->save();
                    }
                }

                // Create Time Operational
                for($t=1;$t<=7;$t++)
                {
                    $time = OperationalTime::firstOrNew([
                        'day_id'=>$t,
                        'outlet_id' => $outlet->id
                    ]);
                    $time->is_24 = $faker->boolean;
                    if(!$time->is_24){
                        $time->is_close = $faker->boolean;
                        if($time->is_close){
                            $time->is_custom_time = true;
                            $time->time_slot = [
                                [
                                    'start' => $faker->randomElement(['06:00','07:00','08:00']),
                                    'end' => $faker->randomElement(['16:00','18:00','20:00'])
                                ]
                            ];
                        }else{
                            $time->is_custom_time = false;
                        }
                    }else{
                        $time->is_close = false;
                        $time->is_custom_time = false;
                    }
                    $time->save();
                }

            }

            DB::commit();

            $this->command->info('Seeding Jagofood Successfully.');
        }catch (\Exception $e){
            DB::rollBack();

            $this->command->info($e->getMessage());
        }
    }
}

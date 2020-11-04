<?php

use Illuminate\Database\Seeder;
use App\Models\Jagofood\Outlet;
use App\Models\Jagofood\Food;
use App\Models\Customer\Customer;
use App\Models\Driver\User as Driver;
use App\Models\Transaction\FoodOrder;
use App\Models\Transaction\FoodOrderDetail;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $success = false;
        // Create Success Order Jagofood
        try {
            for ($i = 0; $i < 10000; $i++) {
                $customer_id = Customer::inRandomOrder()->first()->id;
                $driver_id = Driver::inRandomOrder()->where('type', 'ride')->first()->id;
                $outlet_id = Outlet::inRandomOrder()->first()->id;

                // Create Order
                $order = new FoodOrder();
                $order->customer_id = $customer_id;
                $order->driver_id = ($success) ? $driver_id : null;
                $order->outlet_id = $outlet_id;

                // SUCCess 20 Cancel 10
                $order->status = ($success) ? 20 : 10;

                $order->order_at = $faker->dateTimeBetween('-3 months', 'now');
                $order->success_at = $order->order_at;
                $order->payment_type = 'cash';
                $order->total_food_price = 0;
                $order->discount_code = null;
                $order->total_discount = 0;
                $order->ongkir_price = $faker->randomElement([5000,2000,15000,10000]);
                $order->final_price = 0;
                $order->customer_address = 'Jalan Khayalan Setinggi-Tingginya No.' . rand(1, 400);

                // If Canceled Uncomment
                $order->cancel_reason = (!$success) ? $faker->randomElement(['Menu Habis','Restoran Tutup','Kecelakaan']) : null;

                $order->save();

                //Create Detail Order
                $food = Food::whereHas('category',function($query) use ($outlet_id){
                    $query->where('outlet_id',$outlet_id);
                })->get();

                if($food->count()<=0){
                    //DB::rollBack();
                    continue;
                }
                $total_detail = rand(1,$food->count());

                $sub_total = 0;

                for($a=0;$a<$total_detail;$a++){
                    $detail = new FoodOrderDetail();
                    $detail->food_order_id = $order->id;
                    $detail->food_id = $food[$a]->id;
                    $detail->name = $food[$a]->name;
                    $detail->price = $food[$a]->price;
                    $detail->qty = rand(1,5);
                    $detail->total_price = $detail->price * $detail->qty;
                    $detail->save();
                    $sub_total += $detail->total_price;
                }

                $order->total_food_price = $sub_total;
                $order->final_price = $sub_total + $order->ongkir_price;
                $order->save();

		DB::commit();
            }

            $this->command->info('Data Success Jagofood Order Created Successfully');

        }catch (\Exception $e){
            DB::rollBack();
            $this->command->info($e->getMessage());
        }

        // Create Canceled Order Jagofood

    }
}

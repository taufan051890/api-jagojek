<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try{
            DB::table('employee.users')->insert(
                [
                    'email' => 'admin@jagojek.id',
                    'name' => 'Administrator',
                    'password' => app('hash')->make('password')
                ]
            );

            DB::commit();

            $this->command->info('Create Admin User Successful');
        }catch (\Exception $e){
            DB::rollBack();

            $this->command->info($e->getMessage());
        }
    }
}

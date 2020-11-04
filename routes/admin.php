<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix'=>'admin'],function($admin){
   $admin->post('login','AuthController@login');

   $admin->group(['prefix'=>'customer'],function($customer){
       $customer->get('table','CustomerController@getUserTable');
   });

    /* CRUD VOUCHERS */
   $admin->group(['prefix'=>'voucher'],function($voucher){

       $voucher->post('create','MasterVoucherController@create');

       $voucher->put('update','MasterVoucherController@update');

       $voucher->delete('delete','MasterVoucherCOntroller@destroy');

   });

   $admin->group(['prefix'=>'vehicle'],function($vehicle){

       /* CRUD BRAND */
       $vehicle->group(['prefix'=>'brand'],function($brand){

           $brand->post('create','MasterVehicleBrandController@createBrand');

           $brand->put('update','MasterVehicleBrandController@updateBrand');

           $brand->delete('delete','MasterVehicleBrandController@deleteBrand');

       });

       /* CRUD MODEL */
       $vehicle->group(['prefix'=>'model'],function($vm){

           $vm->post('create','MasterVehicleModelController@create');

           $vm->put('update','MasterVehicleModelController@update');

           $vm->delete('delete','MasterVehicleModelController@delete');

       });

   });
});

// Get List
$router->group(['prefix'=>'list'],function($data){
    // Get Brand Data
    $data->get('vehicle/brand','MasterVehicleBrandController@getBrand');

    // Get Model Data
    $data->get('vehicle/model','MasterVehicleModelController@get');

    // Get Year Data
    $data->get('vehicle/year','MasterVehicleYearController@get');

    // Get Cities & Province
    $data->get('master/city','MasterZoneController@getCityProvince');

    // Get Available Promo
    $data->get('vouchers','MasterVoucherController@getList');

});

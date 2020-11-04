<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix'=>'customer'],function($customer){

    $customer->post('otp/{action}','AuthController@sendOTP');

    $customer->post('register','AuthController@register');

    $customer->post('login','AuthController@login');

    $customer->group(['middleware'=>'auth_customer'],function($auth){
        $auth->get('logout','AuthController@logout');

        //Get Jagocoin
        $auth->get('my_jagocoin','BalanceController@getMyJagocoin');

        //JagoRide
        $auth->group(['prefix'=>'jagoride'],function($jr){

            $jr->post('find_driver','JagorideController@placeOrderRide');

            $jr->get('get/order', 'OrderController@getOrderRide');

            $jr->get('detail/order/{id}', 'OrderController@getDetailOrderRide');

        });

        //Jagofood
        $auth->group(['prefix'=>'jagofood'],function($jf){
            $jf->post('love','JagofoodController@love');

            $jf->post('rate','JagofoodController@rate');

            $jf->get('get/category','JagofoodController@getOutletFoodCategory');

            $jf->get('get/food','JagofoodController@getFoodByCategory');

            $jf->post('get/{by}','JagofoodController@getOutlet');

            $jf->post('place_order','OrderController@placeOrderFood');

            $jf->get('get/food/promo', 'JagofoodController@getFoodPromo');

            $jf->get('get/order', 'OrderController@getOrderFood');

            $jf->get('detail/order/{id}', 'OrderController@getDetailOrderFood');

            $jf->get('get/favourite', 'JagofoodController@getFavouriteOutlet');

            $jf->post('search', 'JagofoodController@search');

        });
    });


});

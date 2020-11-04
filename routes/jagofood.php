<?php

/** @var \Laravel\Lumen\Routing\Router $router */

//Not Login
$router->group(['prefix'=>'jagofood'],function($jagojek){

    //Testing Only
    $jagojek->post('create/user','TestController@createNewUser');

    $jagojek->post('otp/{action}','AuthController@sendOTP');

    $jagojek->post('login','AuthController@login');

    $jagojek->group(['middleware'=>'auth_jagofood'],function($auth){

        $auth->get('logout','AuthController@logout');
        /* Food */
        $auth->get('list/category','FoodController@getListCategory');
        $auth->post('create/category','FoodController@createCategory');
        $auth->put('edit/category','FoodController@editCategory');
        $auth->get('list/food','FoodController@listfood');
        $auth->post('create/food','FoodController@createFood');
        $auth->get('food/promo', 'FoodController@getPromoFood');

        /* Outlet */
        $auth->get('info/outlet','OutletController@getInfoOutlet');
        $auth->post('edit/outlet/banner','OutletController@changeBanner');
        $auth->put('update/outlet/status','OutletController@setOpenStatusOutlet');
        $auth->get('list/operational_time','OutletController@getOperationalTime');
        $auth->post('set/operational_time','OutletController@setOperationalTime');

        /* Prommo */
        $auth->get('history/promo','PromoController@getPromoHistory');
        $auth->post('create/promo/food','PromoController@createPromoProduct');
        $auth->post('create/promo/total','PromoController@createPromoTotal');
        $auth->post('create/promo/ongkir','PromoController@createPromoOngkir');
        $auth->put('stop/promo','PromoController@stopPromo');

        $auth->post('set/pin','ProfileController@setPin');

        /* Order */
        $auth->get('order/history','OrderController@historyOrder');
        $auth->get('detail/order/{id}', 'OrderController@detailOrder');
        $auth->put('accept/order', 'OrderController@acceptOrder');
        $auth->put('cancel/order', 'OrderController@cancelOrder');
        // $auth->put('pickup/order', 'OrderController@pickupOrder');
        // $auth->put('deliver/order', 'OrderController@deliverOrder');
        // $auth->put('arrived/order', 'OrderController@arrivedOrder');
        // $auth->put('finish/order', 'OrderController@finishOrder');

        /* Profile */

        /* Tagihan */

        
    });
});

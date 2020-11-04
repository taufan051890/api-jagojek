<?php

/** @var \Laravel\Lumen\Routing\Router $router */

//Not Login
$router->group(['prefix'=>'jagomart'],function($jagojek){

    //Testing Only
    $jagojek->post('create/user','TestController@createNewUser');

    $jagojek->post('otp/{action}','AuthController@sendOTP');

    $jagojek->post('login','AuthController@login');

    $jagojek->group(['middleware'=>'auth_jagomart'],function($auth){
        $auth->get('logout','AuthController@logout');

        $auth->get('list/category','ItemController@getListCategory');

        $auth->post('create/category','ItemController@createCategory');

        $auth->put('edit/category','ItemController@editCategory');

        $auth->get('list/item','ItemController@listItem');

        $auth->post('create/item','ItemController@createItem');

        $auth->get('info/outlet','OutletController@getInfoOutlet');

        $auth->put('update/outlet/status','OutletController@setOpenStatusOutlet');

        $auth->get('history/promo','PromoController@getPromoHistory');

        $auth->post('create/promo/product','PromoController@createPromoProduct');

        $auth->post('create/promo/total','PromoController@createPromoTotal');

        $auth->post('create/promo/ongkir','PromoController@createPromoOngkir');

        $auth->put('stop/promo','PromoController@stopPromo');

        $auth->get('list/operational_time','OutletController@getOperationalTime');

        $auth->post('set/operational_time','OutletController@setOperationalTime');

        $auth->post('set/pin','ProfileController@setPin');

	$auth->post('edit/outlet/banner','OutletController@changeBanner');

    });
});

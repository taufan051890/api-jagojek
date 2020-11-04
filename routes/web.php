<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'user','namespace'=>'Frontend'], function () use ($router) {
    //Route Send OTP
    $router->get('send-sms-user/{phone}',['uses'=>'UserController@sendOTPMessage']);

    //Route Check Phone Number
    $router->get('cek-phone-used/{phone}',['uses'=>'UserController@checkPhoneNumber']);

    //Register New User
    $router->post('register-user',['uses'=>'UserController@registerUser']);

    //Verify OTP Register
    $router->post('register-otp-verify',['uses'=>'UserController@verifyOTPRegister']);

    //Login  User
    $router->post('login-user-otp','UserController@verifyLoginUser');


});

$router->group(['middleware'=>'auth:user','prefix' => 'outlet','namespace' => 'Frontend'], function () use ($router){

    //Get New Outlets
    $router->post('get-new-outlet','OutletController@getNewOutlets');

    //Get In Demand Outlets
    $router->post('get-best-selling-outlet',['uses'=>'OutletController@getInDemandOutlets']);

    //Get Closets Outlets
    $router->post('get-nearest-outlet',['uses'=>'OutletController@getNearestOutlets']);

    //Get Favorite Outlets
    $router->post('get-fav-outlet',['uses'=>'OutletController@getFavoriteOutlets']);


    //Get Favorite Outlets

    //Get Outlets Info

    //Like Outlets

    //Dislike Outlets

    //Rating Outlets


});

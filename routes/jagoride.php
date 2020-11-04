<?php

/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  8/27/20, 2:26 PM
 *
 */

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix'=>'jagoride'],function($jagoride){

    $jagoride->post('otp/{action}','AuthController@sendOTP');

    /* === Login === */

    $jagoride->post('login','AuthController@login');

    //TODO : Verify OTP
    //TODO : Contact Us

    /* === End Login === */


    /* === Register === */

    $jagoride->post('register','AuthController@registerJagoride');

    /* === End Register === */


    $jagoride->group(['middleware'=>'auth_jagoride'],function($auth){
        /* Profile */
        $auth->get('get/profile', 'AuthController@getProfile');
        /* Phone */
        $auth->post('update/phone/otp','AuthController@sendOTPUpdateNumber');
        $auth->post('update/phone','AuthController@changeNumber');
        
        $auth->group(['namespace'=>'Ride'],function($auth){

            $auth->group(['prefix' => 'motor'], function($auth) {
                $auth->get('list','MotorController@getList');
                $auth->post('add','MotorController@addMotor');
                $auth->post('update', 'MotorController@updateMotor');
                $auth->delete('delete', 'MotorController@deleteMotor');
                $auth->put('use', 'MotorController@useMotor');
            });

            $auth->group(['prefix' => 'order'], function($auth) {
                $auth->get('new', 'OrderController@newOrder');
            });
        });

        /* JAGOFOOD AREA */
        $auth->group(['prefix' => 'jagofood', 'namespace' => 'Jagofood'], function($auth) {
            $auth->get('order/history', 'OrderController@getOrderFoodHistory');
            $auth->put('accept/order', 'OrderController@acceptOrder');
            $auth->put('cancel/order', 'OrderController@cancelOrder');
            $auth->put('pickup/order', 'OrderController@pickupOrder');
            $auth->put('deliver/order', 'OrderController@deliverOrder');
            $auth->put('arrived/order', 'OrderController@arrivedOrder');
            $auth->put('finish/order', 'OrderController@finishOrder');
        });

    });
    /* === Status === */

    // TODO : Ubah Status
    // TODO : Catatan Order

    /* === End Status === */


    /* === Order === */

    // TODO : Riwayat Order
    // TODO : Detail Pembayaran
    // TODO : Order Masuk
    // TODO : Informasi Order Masuk
    // TODO : Informasi Batalkan
    // TODO : Cancel Order
    // TODO : Sudah Dipesan
    // TODO : Sudah Diantar

    /* === End Order === */


    /* === Dompet === */

    // TODO : List Rekening
    // TODO : Tambah Rekening
    // TODO : List Bank
    // TODO : Deposit
    // TODO : Transfer deposit ke rekening
    // TODO : Top Up Saldo Deposit
    // TODO : Riwayat Transaksi Deposit
    // TODO : Jagocoin
    // TODO : Transfer jagocoin ke rekening
    // TODO : Top Up Saldo Jagocoin
    // TODO : Riwayat Transaksi Jagocoin

    /* === End Dompet === */


    /* === Income === */

    // TODO : Riwayat Pendapatan

    /* === End Income === */


    /* === Account === */

    // TODO : Informasi Profile

    /* === End Account === */


    /* === Perform === */

    // TODO : Edit No HP
    // TODO : Edit Kendaraan digunakan
    // TODO : Tambah Kendaraan

    /* === End Perform === */


    /* === Setting === */

    // TODO : Informasi Total Penyelesaian
    // TODO : Informasi Poin Terkumpul
    // TODO : Penilaian
    // TODO : History Ulasan dan Penilaian

    /* === End Setting === */


    /* === Message ==== */

    // TODO : Basic Message

    /* === End Message === */


    /* === PIN === */

    // TODO : Tambah Pin
    // TODO : Edit Pin
    // TODO : Lupa Pin

    /* ===  END PIN === */


    /* === HELP === */

    // TODO : Need Update Requirement & Design

    /* === END HELP === */

});

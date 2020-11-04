<?php
namespace App\Http\Controllers\Jagofood;


class AuthController extends \App\Http\Controllers\AuthController
{

    public function __construct()
    {

        $this->tb_user = 'jagofood.user';
        $this->tb_token = 'jagofood.user_token';
    }

}

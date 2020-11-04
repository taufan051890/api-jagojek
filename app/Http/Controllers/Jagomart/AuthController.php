<?php

namespace App\Http\Controllers\Jagomart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends \App\Http\Controllers\AuthController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->tb_user = 'jagomart.user';
        $this->tb_token = 'jagomart.user_token';

    }

    public function register(Request $request){
        /* Validate User */

    }

}

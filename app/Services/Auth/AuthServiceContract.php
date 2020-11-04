<?php

namespace App\Services\Auth;

interface AuthServiceContract {

    public function generateToken($auth_id,$device=null,$os=null);

    public function validateToken($token);

    public function expireToken($token);

    public function getUserId($token);

    public function getPartnerId($token);


}

<?php

namespace App\Http\Middleware;

use App\Services\Auth\AuthService;
use App\Traits\JsonResponse;
use Closure;

class TokenMiddleware
{
    use JsonResponse;

    private $tokenEngine;

    function __construct()
    {
        $this->tokenEngine = new AuthService();
    }

    public function handle($request, Closure $next, $role)
    {
        $token = $request->bearerToken();
        if($token!=null){

            if(!$this->tokenEngine->validateToken($token)){
                return $this->json200('Invalid Token');
            }

        }else{
            return $this->json500('Unauthorized Action');
        }

        if($role == 'user'){
            $request->attributes->add(['user_id'=>$this->tokenEngine->getUserId($token)]);
        }

        return $next($request);
    }
}

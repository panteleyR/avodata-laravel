<?php

namespace App\Http\Middleware;

use App\Contracts\MainMicroserviceConnector;
use App\Contracts\UserService;
use Closure;

class AuthenticateService
{
    public MainMicroserviceConnector $mainMicroservice;
    public UserService $userService;

    public function __construct(MainMicroserviceConnector $mainMicroservice, UserService $userService)
    {
        $this->mainMicroservice = $mainMicroservice;
        $this->userService = $userService;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            throw new \Exception('unauthenticated');
        }
        $user = $this->mainMicroservice->user($token);
        $this->userService->setData($user);

        return $next($request);
    }
}

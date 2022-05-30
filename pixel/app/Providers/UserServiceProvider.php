<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use App\Contracts\UserService as UserServiceContract;
use App\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UserServiceContract::class, function ($app) {
            return new UserService();
        });
    }
}

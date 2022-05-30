<?php

namespace App\Providers;

use App\Contracts\Client as ClientContract;
use App\Services\Client;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ClientContract::class, function ($app) {
            return new Client($app->get('request'));
        });
    }
}

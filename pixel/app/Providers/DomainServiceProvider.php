<?php

namespace App\Providers;

use App\Contracts\MainMicroserviceConnector;
use App\Services\MainMicroservice;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MainMicroserviceConnector::class, function ($app) {
            return new MainMicroservice();
        });
    }
}

<?php

namespace App\Providers;

use App\Contracts\GeoGuard as GeoGuardContract;
use App\Services\GeoGuard;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class GeoGuardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GeoGuardContract::class, function ($app) {
            return new GeoGuard();
        });
    }
}

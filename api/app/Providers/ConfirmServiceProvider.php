<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Confirm;

class ConfirmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\Confirm', function ($app) {
            return new Confirm();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

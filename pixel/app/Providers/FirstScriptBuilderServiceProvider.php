<?php

namespace App\Providers;

use App\Contracts\FirstScriptBuilder as ScriptBuilderContract;
use App\Services\FirstScriptBuilder;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class FirstScriptBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ScriptBuilderContract::class, function ($app) {
            return new FirstScriptBuilder();
        });
    }
}

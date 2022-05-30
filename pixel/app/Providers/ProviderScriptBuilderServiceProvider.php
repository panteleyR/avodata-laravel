<?php

namespace App\Providers;

use App\Contracts\ProviderScriptBuilder as ScriptBuilderContract;
use App\Services\ProviderScriptBuilder;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class ProviderScriptBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ScriptBuilderContract::class, function ($app) {
            return new ProviderScriptBuilder();
        });
    }
}

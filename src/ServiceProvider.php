<?php

namespace Whitecube\LaravelTimezones;

use Whitecube\LaravelTimezones\Timezone;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(Timezone::class, function ($app) {
            return new Timezone(config('app.timezone'));
        });
    }
}

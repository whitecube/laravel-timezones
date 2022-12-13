<?php

namespace Whitecube\LaravelTimezones;

use Whitecube\LaravelTimezones\Timezone;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('timezone', function ($app) {
            return new Timezone(config('app.timezone'));
        });
    }
}

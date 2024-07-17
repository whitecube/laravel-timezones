<?php

namespace Whitecube\LaravelTimezones\Facades;

use Illuminate\Support\Facades\Facade;

class Timezone extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Whitecube\LaravelTimezones\Timezone::class;
    }
}

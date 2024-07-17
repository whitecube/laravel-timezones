<?php

namespace Whitecube\LaravelTimezones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(mixed $timezone = null)
 * @method static void setCurrent(mixed $timezone = null)
 * @method static \Carbon\CarbonTimeZone current()
 * @method static void setStorage(mixed $timezone = null)
 * @method static \Carbon\CarbonTimeZone storage()
 * @method static \Carbon\CarbonInterface now()
 * @method static \Carbon\CarbonInterface date(mixed $value, callable|null $maker)
 * @method static \Carbon\CarbonInterface store(mixed $value, callable|null $maker)
 */
class Timezone extends Facade
{
    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(): string
    {
        return \Whitecube\LaravelTimezones\Timezone::class;
    }
}

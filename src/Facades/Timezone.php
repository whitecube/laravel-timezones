<?php

namespace Whitecube\LaravelTimezones\Facades;

use Illuminate\Support\Facades\Facade;

class Timezone extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'timezone';
    }
}

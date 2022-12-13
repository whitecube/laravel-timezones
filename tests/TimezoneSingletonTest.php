<?php

use Carbon\CarbonTimeZone;
use Whitecube\LaravelTimezones\Timezone;

it('can create a default Timezone instance and access its current & storage settings', function() {
    $instance = new Timezone('Europe/Brussels');

    expect($instance->getCurrent())->toBeInstanceOf(CarbonTimeZone::class);
    expect($instance->getStorage())->toBeInstanceOf(CarbonTimeZone::class);
});

<?php

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Whitecube\LaravelTimezones\Timezone;

it('can create a default Timezone instance and access its current & storage settings', function() {
    $instance = new Timezone('Europe/Brussels');

    expect($instance->getCurrent())->toBeInstanceOf(CarbonTimeZone::class);
    expect($instance->getStorage())->toBeInstanceOf(CarbonTimeZone::class);
});

it('can convert date with defined timezone to current & storage timezones', function() {
    $instance = new Timezone('UTC');
    $instance->setCurrent('Europe/Brussels');

    $date = new Carbon(null, 'Asia/Phnom_Penh');

    expect($instance->convertToStorage($date)->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($instance->convertToCurrent($date)->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($date->getTimezone()->getName() ?? null)->toBe('Asia/Phnom_Penh');
});

it('can convert date with unset timezone to current & storage timezones', function() {
    $instance = new Timezone('UTC');
    $instance->setCurrent('Europe/Brussels');

    $date = new Carbon();

    expect($instance->convertToStorage($date)->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($instance->convertToCurrent($date)->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($date->getTimezone()->getName() ?? null)->toBe(date_default_timezone_get());
});

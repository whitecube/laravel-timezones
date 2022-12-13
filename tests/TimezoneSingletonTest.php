<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonTimeZone;
use Carbon\CarbonInterface;
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

it('can create date with current timezone', function() {
    $instance = new Timezone('UTC');
    $instance->setCurrent('Europe/Brussels');

    $string = $instance->makeDateWithCurrent('1993-03-16 03:00:00');
    $date = $instance->makeDateWithCurrent(new Carbon('1993-03-16 03:00:00', 'UTC'));
    $custom = $instance->makeDateWithCurrent('1993-03-16 03:00:00', fn($value, $tz) => new CarbonImmutable($value, $tz));

    expect($string)->toBeInstanceOf(CarbonInterface::class);
    expect($string->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($date)->toBeInstanceOf(CarbonInterface::class);
    expect($date->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($custom)->toBeInstanceOf(CarbonImmutable::class);
    expect($custom->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
});

it('can create date with storage timezone', function() {
    $instance = new Timezone('UTC');
    $instance->setCurrent('Europe/Brussels');

    $string = $instance->makeDateWithStorage('1993-03-16 03:00:00');
    $date = $instance->makeDateWithStorage(new Carbon('1993-03-16 03:00:00', 'Europe/Brussels'));
    $custom = $instance->makeDateWithStorage('1993-03-16 03:00:00', fn($value, $tz) => new CarbonImmutable($value, $tz));

    expect($string)->toBeInstanceOf(CarbonInterface::class);
    expect($string->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($date)->toBeInstanceOf(CarbonInterface::class);
    expect($date->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($custom)->toBeInstanceOf(CarbonImmutable::class);
    expect($custom->getTimezone()->getName() ?? null)->toBe('UTC');
});


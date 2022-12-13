<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonTimeZone;
use Carbon\CarbonInterface;
use Whitecube\LaravelTimezones\Timezone;

it('can create a default Timezone instance and access its current & storage settings', function() {
    $instance = new Timezone('Europe/Brussels');

    expect($instance->current())->toBeInstanceOf(CarbonTimeZone::class);
    expect($instance->storage())->toBeInstanceOf(CarbonTimeZone::class);
});

it('can set the application timezone', function() {
    $instance = new Timezone('UTC');

    expect($instance->current()->getName())->toBe('UTC');

    $instance->set('Europe/Brussels');

    expect($instance->current()->getName())->toBe('Europe/Brussels');

    $instance->setCurrent('Europe/Paris');

    expect($instance->current()->getName())->toBe('Europe/Paris');
});

it('can set the database timezone', function() {
    $instance = new Timezone('UTC');

    expect($instance->storage()->getName())->toBe('UTC');

    $instance->setStorage('Europe/Brussels');
    
    expect($instance->storage()->getName())->toBe('Europe/Brussels');
});

it('can get the current date using the application\'s timezone', function() {
    $instance = new Timezone('UTC');
    $instance->set('Europe/Brussels');

    $date = $instance->now();

    expect($date)->toBeInstanceOf(CarbonInterface::class);
    expect($date->getTimezone()->getName())->toBe('Europe/Brussels');
});

it('can create or convert a date using the application\'s current timezone', function() {
    $instance = new Timezone('UTC');
    $instance->set('Europe/Brussels');

    $string = $instance->date('1993-03-16 03:00:00');
    $date = $instance->date(new Carbon('1993-03-16 03:00:00', 'UTC'));
    $custom = $instance->date('1993-03-16 03:00:00', fn($value, $tz) => new CarbonImmutable($value, $tz));

    expect($string)->toBeInstanceOf(CarbonInterface::class);
    expect($string->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($date)->toBeInstanceOf(CarbonInterface::class);
    expect($date->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
    expect($custom)->toBeInstanceOf(CarbonImmutable::class);
    expect($custom->getTimezone()->getName() ?? null)->toBe('Europe/Brussels');
});


it('can create or convert a date using the database\'s storage timezone', function() {
    $instance = new Timezone('UTC');
    $instance->setCurrent('Europe/Brussels');

    $string = $instance->store('1993-03-16 03:00:00');
    $date = $instance->store(new Carbon('1993-03-16 03:00:00', 'Europe/Brussels'));
    $custom = $instance->store('1993-03-16 03:00:00', fn($value, $tz) => new CarbonImmutable($value, $tz));

    expect($string)->toBeInstanceOf(CarbonInterface::class);
    expect($string->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($date)->toBeInstanceOf(CarbonInterface::class);
    expect($date->getTimezone()->getName() ?? null)->toBe('UTC');
    expect($custom)->toBeInstanceOf(CarbonImmutable::class);
    expect($custom->getTimezone()->getName() ?? null)->toBe('UTC');
});


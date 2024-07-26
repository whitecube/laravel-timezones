<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Whitecube\LaravelTimezones\Casts\TimezonedDatetime;

it('can access UTC database date with application timezone', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = '2022-12-15 09:00:00';

    $output = $cast->get(fakeModel(), 'id', $input, []);

    expect($output->getTimezone()->getName())->toBe('Europe/Brussels');
    expect($output->format('Y-m-d H:i:s'))->toBe('2022-12-15 10:00:00');
});

it('can access UTC database date with application timezone and specific format', function() {
    setupFacade();

    $cast = new TimezonedDatetime('d/m/Y H:i');

    $input = '15/12/2022 09:00';

    $output = $cast->get(fakeModel(), 'id', $input, []);

    expect($output->getTimezone()->getName())->toBe('Europe/Brussels');
    expect($output->format('Y-m-d H:i:s'))->toBe('2022-12-15 10:00:00');
});

it('can access NULL date as NULL', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = null;

    $output = $cast->get(fakeModel(), 'id', $input, []);

    expect($output)->toBeNull();
});

it('can access empty string as NULL', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = '';

    $output = $cast->get(fakeModel(), 'id', $input, []);

    expect($output)->toBeNull();
});

it('can mutate application timezone datetime string to UTC database date string', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = '2022-12-15 10:00:00';

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBe('2022-12-15 09:00:00');
});

it('can mutate application timezone date instance to UTC database date string', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = new \Carbon\Carbon('2022-12-15 10:00:00', 'Europe/Brussels');

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBe('2022-12-15 09:00:00');
});

it('can mutate UTC date instance to UTC database date string', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = new \Carbon\Carbon('2022-12-15 09:00:00', 'UTC');

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBe('2022-12-15 09:00:00');
});

it('can mutate date instance with exotic timezone to UTC database date string', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = new \Carbon\Carbon('2022-12-15 04:00:00', 'America/Toronto');

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBe('2022-12-15 09:00:00');
});

it('can mutate NULL as NULL', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = null;

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBeNull();
});

it('can mutate empty string as NULL', function() {
    setupFacade();

    $cast = new TimezonedDatetime();

    $input = '';

    $output = $cast->set(fakeModel(), 'id', $input, []);

    expect($output)->toBeNull();
});


it('can mutate 0 values', function() {
    // 4 hours difference between dubai and UTC
    setupFacade(current: 'Asia/Dubai');
    
    $cast = new TimezonedDatetime('H');

    $input = 0;
    
    $output = $cast->set(fakeModel(), 'id', $input, []);
    expect($output)->toEqual(20);

    $output = $cast->get(fakeModel(), 'id', $input, []);
    expect($output->format('H'))->toEqual(4);
});

test('a model with a timezone date cast can be json serialized', function () {
    setupFacade();

    Config::shouldReceive('get')
        ->with('app.timezone')
        ->andReturn('UTC');

    $date = new Carbon('2022-12-15 09:00:00', 'UTC');
    $model = fakeModelWithCast();

    $model->test_at = $date;
    $model->updated_at = $date;

    expect($model->jsonSerialize())
        ->toBe([
            'test_at' => $date->toJSON(),
            'updated_at' => $date->toJSON()
        ]);
});

test('a model with a timezone date cast can parse ISO-formatted values properly', function () {
    setupFacade();

    Config::shouldReceive('get')
        ->with('app.timezone')
        ->andReturn('UTC');

    $date = new Carbon('2022-12-15 09:00:00', 'UTC');
    $model = fakeModelWithCast();

    $model->test_at = $date->toIso8601String();
    $model->updated_at = $date->toIso8601String();

    expect($model->jsonSerialize())
        ->toBe([
            'test_at' => $date->toJSON(),
            'updated_at' => $date->toJSON()
        ]);
});

test('a model with a timezone date cast can parse datetime values properly', function () {
    setupFacade();

    Config::shouldReceive('get')
        ->with('app.timezone')
        ->andReturn('UTC');

    $date = new DateTime('2022-12-15 09:00:00');
    $model = fakeModelWithCast();

    $model->test_at = $date;
    $model->updated_at = $date;

    expect($model->jsonSerialize())
        ->toBe([
            'test_at' => '2022-12-15T09:00:00.000000Z',
            'updated_at' => '2022-12-15T09:00:00.000000Z'
        ]);
});

test('a model with a timezone date cast can parse datetime values with a defined timezone properly', function () {
    setupFacade();

    Config::shouldReceive('get')
        ->with('app.timezone')
        ->andReturn('UTC');

    $date = new DateTime('2022-12-15 09:00:00', new DateTimeZone('Asia/Taipei'));
    $model = fakeModelWithCast();

    $model->test_at = $date;
    $model->updated_at = $date;

    expect($model->jsonSerialize())
        ->toBe([
            'test_at' => '2022-12-15T01:00:00.000000Z',
            'updated_at' => '2022-12-15T01:00:00.000000Z'
        ]);
});

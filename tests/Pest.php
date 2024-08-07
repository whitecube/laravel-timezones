<?php

use Whitecube\LaravelTimezones\Timezone;
use Whitecube\LaravelTimezones\Facades\Timezone as Facade;
use Illuminate\Database\Eloquent\Model;
use Whitecube\LaravelTimezones\Casts\TimezonedDatetime;
use Whitecube\LaravelTimezones\Concerns\HasTimezonedTimestamps;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(\Whitecube\LaravelTimezones\Tests\TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//     return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function setupFacade(string $storage = 'UTC', string $current = 'Europe/Brussels')
{
    $instance = new Timezone($storage);
    $instance->set($current);

    Facade::swap($instance);
}

function fakeModel()
{
    return new class() extends Model {
        public function getDateFormat()
        {
            return 'Y-m-d H:i:s';
        }
    };
}

function fakeModelWithCast()
{
    return new class() extends Model {
        use HasTimezonedTimestamps;
        
        protected $casts = [
            'test_at' => TimezonedDatetime::class,
            'created_at' => TimezonedDatetime::class,
            'updated_at' => TimezonedDatetime::class,
        ];

        public function getDateFormat()
        {
            return 'Y-m-d H:i:s';
        }
    };
}

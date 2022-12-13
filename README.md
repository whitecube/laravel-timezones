# Laravel Timezones

## Getting started

All database dates should be stored using the `app.timezone` config setting. We highly suggest keeping it as `UTC` since it's a global standard for dates storage.

For in-app date manipulation and display, you can define the timezone all dates should cast to using one of the following methods. Depending on you app's context, choose the one that best suits your situation.

### 1. Using middleware

Useful when the app's timezone should be set by ther user's settings.

### 2. Using a Service Provider

Useful when the app's timezone should be set by the application itself.

## Usage

Once everything's setup, the easiest way to manipulate dates configured with the app's current timezone is to use the `TimezonedDatetime` or `ImmutableTimezonedDatetime` cast types on your models:

```php
/**
 * The attributes that should be cast.
 *
 * @var array
 */
protected $casts = [
    'published_at' => \Whitecube\LaravelTimezones\Casts\TimezonedDatetime::class,
    'birthday' => \Whitecube\LaravelTimezones\Casts\ImmutableTimezonedDatetime::class . ':Y-m-d',
];
```

In other scenarios, use the `Timezone` Facade directly for conversion:

```php
use Carbon\Carbon;
use Whitecube\LaravelTimezones\Facades\Timezone;

// Get the current date configured with the current timezone:
$now = Timezone::now();

// Create a date using the current timezone:
$date = Timezone::date('2023-01-01 00:00:00');
// Alternatively, set the timezone manually on a Carbon instance:
$date = new Carbon('2023-01-01 00:00:00', Timezone::current());


// Convert a date to the current timezone:
$date = Timezone::date(new Carbon('2023-01-01 00:00:00', 'UTC'));
// Alternatively, set the application timezone yourself:
$date = (new Carbon('2023-01-01 00:00:00', 'UTC'))->setTimezone(Timezone::current());

// Convert a date to the storage timezone:
$date = Timezone::store(new Carbon('2023-01-01 00:00:00', 'Europe/Brussels'));
// Alternatively, set the storage timezone yourself:
$date = (new Carbon('2023-01-01 00:00:00', 'Europe/Brussels'))->setTimezone(Timezone::storage());
```

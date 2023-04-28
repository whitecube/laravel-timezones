# Laravel Timezones

Dealing with timezones can be a frustrating experience. Here's an attempt to brighten your day.

**The problem:** it is commonly agreed that dates should be stored as `UTC` datetimes in the database, which generally means they also need to be adapted for the local timezone before manipulation or display. Laravel provides a `app.timezone` configuration, making it possible to start working with timezones. However, changing that configuration will affect both the stored and manipulated date's timezones. This package tries to address this by providing a timezone conversion mechanism that should perform most of the repetitive timezone configurations out of the box.

```php
// Model:
protected $casts = [
    'occurred_at' => TimezonedDatetime::class,
];

// Set a custom timezone
Timezone::set('Europe/Brussels');

// Display dates stored as UTC in the app's timezone:
// (database value: 2022-12-13 09:00:00)
echo $model->occurred_at->format('d.m.Y H:i'); // Output: 13.12.2022 10:00

// Store dates using automatic UTC conversion:
$model->occurred_at = '2022-12-13 20:00:00';
$model->save(); // Database value: 2022-12-13 19:00:00
```

## Installation

```bash
composer require whitecube/laravel-timezones
```

## Getting started

The `app.timezone` configuration setting has to be set to the timezone that should be used when saving dates in the database. We highly recommend keeping it as `UTC` since it's a global standard for dates storage.

For in-app date manipulation and display, one would expect more flexibility. That's why it is possible to set the application's timezone dynamically by updating the `timezone` singleton instance. Depending on the app's context, please choose one that suits your situation best:

### 1. Using middleware

Useful when the app's timezone should be set by ther user's settings.

```php
namespace App\Http\Middleware;
 
use Closure;
use Whitecube\LaravelTimezones\Facades\Timezone;
 
class DefineApplicationTimezone
{
    public function handle($request, Closure $next)
    {
        Timezone::set($request->user()->timezone ?? 'Europe/Brussels');
 
        return $next($request);
    }
}
```

### 2. Using a Service Provider

Useful when the app's timezone should be set by the application itself. For instance, in `App\Providers\AppServiceProvider`:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Whitecube\LaravelTimezones\Facades\Timezone;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Timezone::set('America/Toronto');
    }
}
```

## Usage

Once everything's setup, the easiest way to manipulate dates configured with the app's current timezone is to use the `TimezonedDatetime` or `ImmutableTimezonedDatetime` cast types on your models:

```php
use Whitecube\LaravelTimezones\Casts\TimezonedDatetime;
use Whitecube\LaravelTimezones\Casts\ImmutableTimezonedDatetime;

/**
 * The attributes that should be cast.
 *
 * @var array
 */
protected $casts = [
    'published_at' => TimezonedDatetime::class,
    'birthday' => ImmutableTimezonedDatetime::class . ':Y-m-d',
];
```

In other scenarios, feel free to use the `Timezone` Facade directly for more convenience:

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

## Assigning values to cast attributes

Many developers are used to assign Carbon instances to date attributes:

```php
$model->published_at = Carbon::create($request->published_at);
```

**This can lead to unexpected behavior** because the assigned Carbon instance will default to the `UTC` timezone, whereas the provided value was probably meant for another timezone. The datetime string will be stored as-is without shifting its timezone accordingly first. 

In order to prevent this, it is recommended to let the Cast do the heavy lifting:

```php
$model->published_at = $request->published_at;
```

The package will now treat the provided datetime string using the correct Timezone (for instance, `Europe/Brussels`) and store the shifted `UTC` value in the database correctly.

A more verbose (but also correct) method would be to create the Carbon instance using the `Timezone` facade :

```php
$model->published_at = Carbon::create($request->published_at, Timezone::current());
// Or, shorthand:
$model->published_at = Timezone::date($request->published_at);
```

**This is not a bug**, it is intended behavior since one should be fully aware of the Carbon instance's timezone before assigning it.

### Edge cases

If you need to use the `TimezonedDatetime` cast on the timestamp columns (`created_at` and/or `updated_at`) AND you're expecting to handle dates with timezones other than UTC or the one you've defined with `Timezone::set()`, you will need to apply the `Whitecube\LaravelTimezones\Concerns\HasTimezonedTimestamps` trait on your model.

This is necessary to prevent Laravel's casting of those attributes to occur, which would transform the value in a way where the timezone information is lost, preventing our cast from working properly.

An example of a case where you need to use the trait:

```php
Timezone::set('Europe/Brussels');

$model->created_at = new Carbon('2022-12-15 09:00:00', 'Asia/Taipei');
```


## 🔥 Sponsorships 

If you are reliant on this package in your production applications, consider [sponsoring us](https://github.com/sponsors/whitecube)! It is the best way to help us keep doing what we love to do: making great open source software.

## Contributing

Feel free to suggest changes, ask for new features or fix bugs yourself. We're sure there are still a lot of improvements that could be made, and we would be very happy to merge useful pull requests. Thanks!

## Made with ❤️ for open source

At [Whitecube](https://www.whitecube.be) we use a lot of open source software as part of our daily work.
So when we have an opportunity to give something back, we're super excited!

We hope you will enjoy this small contribution from us and would love to [hear from you](mailto:hello@whitecube.be) if you find it useful in your projects. Follow us on [Twitter](https://twitter.com/whitecube_be) for more updates!

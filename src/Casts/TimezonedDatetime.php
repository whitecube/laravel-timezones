<?php

namespace Whitecube\LaravelTimezones\Casts;

use Whitecube\LaravelTimezones\Facades\Timezone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Whitecube\LaravelTimezones\DatetimeParser;

class TimezonedDatetime implements CastsAttributes
{
    /**
     * A developer-specific format to use for string parsing.
     */
    protected ?string $format;

    /**
     * Create a new casting instance.
     */
    public function __construct(?string $format = null)
    {
        $this->format = $format;
    }

    /**
     * Cast the given value.
     *
     * @return \Carbon\CarbonInterface
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }

        if ($this->isTimestamp($model, $key)) {
            $value = Carbon::parse($value)->format($this->format ?? $model->getDateFormat());
        }

        $original = Timezone::store($value, fn($raw, $tz) => $this->asDateTime($raw, $tz, $model));

        return Timezone::date($original);
    }

    /**
     * Prepare the given value for storage.
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }

        if ($this->isTimestamp($model, $key) && is_string($value)) {
            $value = Carbon::parse($value, Config::get('app.timezone'));
        }

        $requested = Timezone::date($value, fn($raw, $tz) => $this->asDateTime($raw, $tz, $model));

        return Timezone::store($requested)->format($this->format ?? $model->getDateFormat());
    }

    /**
     * Check if the given key is part of the model's known timestamps.
     */
    protected function isTimestamp(Model $model, string $key): bool
    {
        return $model->usesTimestamps() && in_array($key, $model->getDates());
    }

    /**
     * Create a new date value from raw material.
     */
    public function asDateTime(mixed $value, CarbonTimeZone $timezone, Model $model): CarbonInterface
    {
        $date = (new DatetimeParser)->parse($value, $this->format ?? $model->getDateFormat());

        if ($this->hasTimezone($value)) {
            return $date->setTimezone($timezone);
        }

        return $date->shiftTimezone($timezone);
    }

    /**
     * Check if the provided value contains timezone information.
     */
    protected function hasTimezone(mixed $value): bool
    {
        return (is_string($value) && array_key_exists('zone', date_parse($value)))
            || (is_a($value, DateTime::class) && $value->getTimezone());
    }
}

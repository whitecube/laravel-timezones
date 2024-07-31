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
     *
     * @var null|string
     */
    protected $format;

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
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return CarbonInterface
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }

        if ($this->isTimestamp($model, $key)) {
            $value = Carbon::parse($value)->format($this->format ?? $model->getDateFormat());
        }

        $original = Timezone::store($value, function ($raw, $tz) use ($model) {
            return $this->asDateTime($raw, $tz, $model);
        });

        return Timezone::date($original);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }

        if ($this->isTimestamp($model, $key) && is_string($value)) {
            $value = Carbon::parse($value, Config::get('app.timezone'));
        }

        $requested = Timezone::date($value, function ($raw, $tz) use ($model) {
            return $this->asDateTime($raw, $tz, $model);
        });

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
     *
     * @param  mixed  $value
     * @param  CarbonTimeZone  $timezone
     * @param  Model  $model
     * @return CarbonInterface
     */
    public function asDateTime($value, CarbonTimeZone $timezone, Model $model): CarbonInterface
    {
        $date = (new DatetimeParser)->parse($value, $this->format ?? $model->getDateFormat());

        if ($this->hasTimezone($value)) {
            return $date->setTimezone($timezone);
        }

        return $date->shiftTimezone($timezone);
    }

    /**
     * Check if the provided value contains timezone information.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function hasTimezone($value): bool
    {
        return (is_string($value) && array_key_exists('zone', date_parse($value)))
            || (is_a($value, DateTime::class) && $value->getTimezone());
    }
}

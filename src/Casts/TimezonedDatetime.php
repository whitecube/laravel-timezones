<?php

namespace Whitecube\LaravelTimezones\Casts;

use Illuminate\Support\Facades\Date;
use Whitecube\LaravelTimezones\Facades\Timezone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TimezonedDatetime implements CastsAttributes
{
    /**
     * A developer-specific format to use for string parsing
     *
     * @var null|string
     */
    protected ?string $format;

    /**
     * Create a new casting instance.
     *
     * @param null|string $format
     * @return void
     */
    public function __construct(?string $format = null)
    {
        $this->format = $format;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Carbon\CarbonInterface
     */
    public function get($model, $key, $value, $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }
        
        $original = Timezone::store($value, fn($raw, $tz) => $this->asDateTime($raw, $tz, $model));

        return Timezone::date($original);
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if(!$value && $value !== 0) {
            return null;
        }

        $requested = Timezone::date($value, fn($raw, $tz) => $this->asDateTime($raw, $tz, $model));

        return Timezone::store($requested)->format($this->format ?? $model->getDateFormat());
    }
 
    /**
     * Create a new date value from raw material
     *
     * @param  mixed  $value
     * @param  Carbon\CarbonTimeZone  $timezone
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Carbon\CarbonInterface
     */
    public function asDateTime($value, $timezone, $model)
    {
        return Date::createFromFormat(
            $this->format ?? $model->getDateFormat(),
            $value,
            $timezone,
        );
    }
}
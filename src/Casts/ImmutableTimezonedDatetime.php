<?php

namespace Whitecube\LaravelTimezones\Casts;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class ImmutableTimezonedDatetime extends TimezonedDatetime
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return CarbonImmutable|CarbonInterface|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if($date = parent::get($model, $key, $value, $attributes)) {
            return $date->toImmutable();
        }

        return $date;
    }
}
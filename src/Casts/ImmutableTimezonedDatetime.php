<?php

namespace Whitecube\LaravelTimezones\Casts;

class ImmutableTimezonedDatetime extends TimezonedDatetime
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        if($date = parent::get($model, $key, $value, $attributes)) {
            return $date->toImmutable();
        }

        return $date;
    }
}
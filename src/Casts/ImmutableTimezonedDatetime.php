<?php

namespace Whitecube\LaravelTimezones\Casts;

use Illuminate\Database\Eloquent\Model;

class ImmutableTimezonedDatetime extends TimezonedDatetime
{
    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if($date = parent::get($model, $key, $value, $attributes)) {
            return $date->toImmutable();
        }

        return $date;
    }
}
<?php

namespace Whitecube\LaravelTimezones\Concerns;

use Whitecube\LaravelTimezones\Casts\TimezonedDatetime;

trait HasTimezonedTimestamps
{
    /**
     * Determine if the given attribute is a date or date castable.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isDateAttribute($key)
    {
        return (in_array($key, $this->getDates(), true) ||
            $this->isDateCastable($key)) &&
            ! $this->hasTimezonedDatetimeCast($key);
    }

    /**
     * Check if key is a timezoned datetime cast
     * 
     * @param string $key 
     * @return bool 
     */
    protected function hasTimezonedDatetimeCast(string $key): bool
    {
        $casts = $this->getCasts();

        return array_key_exists($key, $casts) && $casts[$key] === TimezonedDatetime::class;
    }
}
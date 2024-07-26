<?php

namespace Whitecube\LaravelTimezones\Concerns;

use Whitecube\LaravelTimezones\Casts\TimezonedDatetime;
use Whitecube\LaravelTimezones\Casts\ImmutableTimezonedDatetime;

trait HasTimezonedTimestamps
{
    /**
     * Determine if the given attribute is a date or date castable.
     */
    protected function isDateAttribute($key)
    {
        return (in_array($key, $this->getDates(), true) ||
            $this->isDateCastable($key)) &&
            ! $this->hasTimezonedDatetimeCast($key);
    }

    /**
     * Check if key is a timezoned datetime cast.
     */
    protected function hasTimezonedDatetimeCast(string $key): bool
    {
        $cast = $this->getCasts()[$key] ?? null;

        if (! $cast) {
            return false;
        }

        $castClassName = explode(':', $cast)[0];

        return in_array(
            $castClassName,
            [TimezonedDatetime::class, ImmutableTimezonedDatetime::class]
        );
    }
}

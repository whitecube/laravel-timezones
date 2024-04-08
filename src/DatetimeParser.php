<?php

namespace Whitecube\LaravelTimezones;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Carbon;
use Carbon\CarbonImmutable;

class DatetimeParser
{
    use HasAttributes;

    /**
     * The model's date storage format
     */
    protected ?string $format;

    /**
     * Parse the value into a carbon instance
     * 
     * @param mixed $value 
     * @param null|string $format 
     * @return Carbon|CarbonImmutable
     */
    public function parse(mixed $value, ?string $format): Carbon|CarbonImmutable
    {
        $this->format = $format;

        return $this->asDateTime($value);
    }


    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->format;
    }
}

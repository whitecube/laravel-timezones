<?php

namespace Whitecube\LaravelTimezones;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Carbon;

class DatetimeParser
{
    use HasAttributes;

    protected ?string $format;

    public function parse(mixed $value, ?string $format): Carbon
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
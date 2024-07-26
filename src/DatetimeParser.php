<?php

namespace Whitecube\LaravelTimezones;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Carbon\CarbonInterface;

class DatetimeParser
{
    use HasAttributes;

    /**
     * The model's date storage format.
     */
    protected ?string $format;

    /**
     * Parse the value into a carbon instance.
     */
    public function parse(mixed $value, ?string $format): CarbonInterface
    {
        $this->format = $format;

        return $this->asDateTime($value);
    }

    /**
     * Get the format for database stored dates.
     */
    public function getDateFormat(): ?string
    {
        return $this->format;
    }
}

<?php

namespace Whitecube\LaravelTimezones;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Carbon\CarbonInterface;

class DatetimeParser
{
    use HasAttributes;

    /**
     * The model's date storage format.
     *
     * @var null|string
     */
    protected $format;

    /**
     * Parse the value into a carbon instance.
     *
     * @param  mixed  $value
     * @param  string|null  $format
     * @return CarbonInterface
     */
    public function parse($value, ?string $format): CarbonInterface
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

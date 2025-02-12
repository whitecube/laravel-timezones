<?php

namespace Whitecube\LaravelTimezones;

use Carbon\CarbonTimeZone;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Traits\Macroable;

class Timezone
{
    use Macroable;

    /**
     * The app's current display & manipulation timezone.
     */
    protected CarbonTimeZone $current;
    /**
     * The app's current storage timezone.
     */
    protected CarbonTimeZone $storage;

    /**
     * Create a new singleton instance.
     */
    public function __construct(string $default = '')
    {
        if (empty($default)) {
            $default = config('app.timezone');
        }

        $this->setStorage($default);
        $this->setCurrent($default);
    }

    /**
     * @alias setCurrent
     *
     * Set the current application timezone.
     */
    public function set(mixed $timezone = null): void
    {
        $this->setCurrent($timezone);
    }

    /**
     * Set the current application timezone.
     */
    public function setCurrent(mixed $timezone): void
    {
        $this->current = $this->makeTimezone($timezone);
    }

    /**
     * Return the current application timezone.
     */
    public function current(): CarbonTimeZone
    {
        return $this->current;
    }

    /**
     * Set the current database timezone.
     */
    public function setStorage(mixed $timezone): void
    {
        $this->storage = $this->makeTimezone($timezone);
    }

    /**
     * Return the current application timezone.
     */
    public function storage(): CarbonTimeZone
    {
        return $this->storage;
    }

    /**
     * Get the current timezoned date.
     */
    public function now(): CarbonInterface
    {
        return $this->convertToCurrent(Date::now());
    }

    /**
     * Configure given date for the application's current timezone.
     */
    public function date(mixed $value, ?callable $maker = null): CarbonInterface
    {
        return $this->makeDateWithCurrent($value, $maker);
    }

    /**
     * Configure given date for the database storage timezone.
     */
    public function store(mixed $value, ?callable $maker = null): CarbonInterface
    {
        return $this->makeDateWithStorage($value, $maker);
    }

    /**
     * Duplicate the given date and shift its timezone to the application's current timezone.
     */
    protected function convertToCurrent(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->current());
    }

    /**
     * Duplicate the given date and shift its timezone to the database's storage timezone.
     */
    protected function convertToStorage(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->storage());
    }

    /**
     * Create or configure date using the application's current timezone.
     */
    protected function makeDateWithCurrent(mixed $value, ?callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToCurrent($value)
            : $this->makeDate($value, $this->current(), $maker);
    }

    /**
     * Create or configure date using the database's storage timezone.
     */
    protected function makeDateWithStorage(mixed $value, ?callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToStorage($value)
            : $this->makeDate($value, $this->storage(), $maker);
    }

    /**
     * Create a date using the provided timezone.
     */
    protected function makeDate(mixed $value, CarbonTimeZone $timezone, ?callable $maker = null): CarbonInterface
    {
        return ($maker)
            ? call_user_func($maker, $value, $timezone)
            : Date::create($value, $timezone);
    }

    /**
     * Create a Carbon timezone from given value.
     */
    protected function makeTimezone(mixed $value): CarbonTimeZone
    {
        if(! is_a($value, CarbonTimeZone::class)) {
            $value = new CarbonTimeZone($value);
        }

        return $value;
    }
}

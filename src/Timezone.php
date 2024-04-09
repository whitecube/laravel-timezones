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
     * The app's current display & manipulation timezone
     *
     * @var \Carbon\CarbonTimeZone
     */
    protected CarbonTimeZone $current;
    /**
     * The app's current storage timezone
     *
     * @var \Carbon\CarbonTimeZone
     */
    protected CarbonTimeZone $storage;

    /**
     * Create a new singleton instance.
     *
     * @param string $default
     * @return void
     */
    public function __construct(string $default)
    {
        $this->setStorage($default);
        $this->setCurrent($default);
    }

    /**
     * @alias setCurrent
     *
     * Set the current application timezone.
     *
     * @param mixed $timezone
     * @return void
     */
    public function set($timezone = null)
    {
        $this->setCurrent($timezone);
    }

    /**
     * Set the current application timezone.
     *
     * @param mixed $timezone
     * @return void
     */
    public function setCurrent($timezone)
    {
        $this->current = $this->makeTimezone($timezone);
    }

    /**
     * Return the current application timezone.
     *
     * @return \Carbon\CarbonTimeZone
     */
    public function current(): CarbonTimeZone
    {
        return $this->current;
    }

    /**
     * Set the current database timezone.
     *
     * @param mixed $timezone
     * @return void
     */
    public function setStorage($timezone)
    {
        $this->storage = $this->makeTimezone($timezone);
    }

    /**
     * Return the current application timezone.
     *
     * @return \Carbon\CarbonTimeZone
     */
    public function storage(): CarbonTimeZone
    {
        return $this->storage;
    }

    /**
     * Get the current timezoned date.
     *
     * @return \Carbon\CarbonInterface
     */
    public function now(): CarbonInterface
    {
        return $this->convertToCurrent(Date::now());
    }

    /**
     * Configure given date for the application's current timezone.
     *
     * @param mixed $value
     * @param null|callable $maker
     * @return \Carbon\CarbonInterface
     */
    public function date($value, callable $maker = null): CarbonInterface
    {
        return $this->makeDateWithCurrent($value, $maker);
    }

    /**
     * Configure given date for the database storage timezone.
     *
     * @param mixed $value
     * @param null|callable $maker
     * @return \Carbon\CarbonInterface
     */
    public function store($value, callable $maker = null): CarbonInterface
    {
        return $this->makeDateWithStorage($value, $maker);
    }

    /**
     * Duplicate the given date and shift its timezone to the application's current timezone.
     *
     * @param \Carbon\CarbonInterface
     * @return \Carbon\CarbonInterface
     */
    protected function convertToCurrent(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->current());
    }

    /**
     * Duplicate the given date and shift its timezone to the database's storage timezone.
     *
     * @param \Carbon\CarbonInterface
     * @return \Carbon\CarbonInterface
     */
    protected function convertToStorage(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->storage());
    }

    /**
     * Create or configure date using the application's current timezone.
     *
     * @param mixed $value
     * @param null|callable $maker
     * @return \Carbon\CarbonInterface
     */
    protected function makeDateWithCurrent($value, callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToCurrent($value)
            : $this->makeDate($value, $this->current(), $maker);
    }

    /**
     * Create or configure date using the database's storage timezone.
     *
     * @param mixed $value
     * @param null|callable $maker
     * @return \Carbon\CarbonInterface
     */
    protected function makeDateWithStorage($value, callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToStorage($value)
            : $this->makeDate($value, $this->storage(), $maker);
    }

    /**
     * Create a date using the provided timezone.
     *
     * @param mixed $value
     * @param \Carbon\CarbonTimeZone $timezone
     * @param null|callable $maker
     * @return \Carbon\CarbonInterface
     */
    protected function makeDate($value, CarbonTimeZone $timezone, callable $maker = null): CarbonInterface
    {
        return ($maker)
            ? call_user_func($maker, $value, $timezone)
            : Date::create($value, $timezone);
    }

    /**
     * Create a Carbon timezone from given value.
     *
     * @param mixed $value
     * @return \Carbon\CarbonTimeZone
     */
    protected function makeTimezone($value): CarbonTimeZone
    {
        if(! is_a($value, CarbonTimeZone::class)) {
            $value = new CarbonTimeZone($value);
        }

        return $value;
    }
}

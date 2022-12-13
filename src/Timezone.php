<?php

namespace Whitecube\LaravelTimezones;

use Carbon\CarbonTimeZone;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

class Timezone
{
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

    public function __construct(string $default)
    {
        $this->setStorage($default);
        $this->setCurrent($default);
    }

    public static function instance(): static
    {
        return app()->make(self::class);
    }

    public static function set($timezone = null)
    {
        static::instance()->setCurrent($timezone);
    }

    public static function current(): CarbonTimeZone
    {
        return static::instance()->getCurrent();
    }

    public static function storage(): CarbonTimeZone
    {
        return static::instance()->getStorage();
    }

    public static function now(): CarbonInterface
    {
        return static::instance()->convertToCurrent(now());
    }

    public static function date($value, callable $maker = null): CarbonInterface
    {
        $instance = static::instance();

        return $instance->convertToCurrent(
            $instance->makeDateWithStorage($value, $maker)
        );
    }

    public static function store($value, callable $maker = null): CarbonInterface
    {
        $instance = static::instance();

        return $instance->convertToStorage(
            $instance->makeDateWithCurrent($value, $maker)
        );
    }

    public function setCurrent($timezone)
    {
        $this->current = $this->makeTimezone($timezone);
    }

    public function getCurrent(): CarbonTimeZone
    {
        return $this->current;
    }

    public function setStorage($timezone)
    {
        $this->storage = $this->makeTimezone($timezone);
    }

    public function getStorage(): CarbonTimeZone
    {
        return $this->storage;
    }

    public function convertToCurrent(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->getCurrent());
    }

    public function convertToStorage(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->setTimezone($this->getStorage());
    }

    public function makeDateWithCurrent($value, callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToCurrent($value)
            : $this->makeDate($value, $this->getCurrent(), $maker);
    }

    public function makeDateWithStorage($value, callable $maker = null): CarbonInterface
    {
        return is_a($value, CarbonInterface::class)
            ? $this->convertToStorage($value)
            : $this->makeDate($value, $this->getStorage(), $maker);
    }

    protected function makeDate($value, CarbonTimeZone $timezone, callable $maker = null): CarbonInterface
    {
        return ($maker)
            ? call_user_func($maker, $value, $timezone)
            : Date::create($value, $timezone);
    }

    protected function makeTimezone($value): CarbonTimeZone
    {
        if(! is_a($value, CarbonTimeZone::class)) {
            $value = new CarbonTimeZone($value);
        }

        return $value;
    }
}

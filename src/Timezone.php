<?php

namespace Whitecube\LaravelTimezones;

use Carbon\CarbonTimeZone;
use Carbon\CarbonInterface;

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

    public function instance(): static
    {
        return app()->make(self::class);
    }

    public function __construct(string $default)
    {
        $this->setStorage($default);
        $this->setCurrent($default);
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

    protected function makeTimezone($value): CarbonTimeZone
    {
        if(! is_a($value, CarbonTimeZone::class)) {
            $value = new CarbonTimeZone($value);
        }

        return $value;
    }
}

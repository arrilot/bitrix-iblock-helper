<?php

namespace Arrilot\BitrixIblockHelper;

use CPHPCache;

trait Cacheable
{
    /**
     * Хранилище полученных из базы ID.
     *
     * @var array
     */
    protected static $values;

    /**
     * Время кэширования списка.
     *
     * @var float|int
     */
    protected static $cacheMinutes = 0;

    /**
     * Директория где хранится кэш.
     *
     * @return string
     */
    protected static function getCacheDir()
    {
        return '/arrilot_bih';
    }

    /**
     * Setter for $cacheMinutes
     *
     * @param $minutes
     */
    public static function setCacheTime($minutes)
    {
        static::$cacheMinutes = $minutes;
    }

    /**
     * Flushes local cache
     */
    public static function flushLocalCache()
    {
        static::$values = null;
    }
    
    /**
     * Flushes local cache
     */
    public static function flushExternalCache()
    {
        (new CPHPCache())->CleanDir(static::getCacheDir());
    }
}

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
     * Время кэширования списка в секундах.
     *
     * @var float|int
     */
    protected static $cacheTtl = 0;

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
     * Setter for $cacheTtl
     *
     * @param $seconds
     */
    public static function setCacheTime($seconds)
    {
        static::$cacheTtl = $seconds;
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

<?php

namespace Arrilot\BitrixIBlockHelper;

use Arrilot\BitrixCacher\Cache;
use Bitrix\Main\Application;
use RuntimeException;

class IblockId
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
     * Получение ID инфоблока по коду (или по коду и типу).
     * Помогает вовремя обнаруживать опечатки.
     *
     * @param string $code
     * @param string|null $type
     * @return int
     *
     * @throws RuntimeException
     */
    public static function getByCode($code, $type = null)
    {
        if (is_null(static::$values)) {
            static::$values = static::getAllByCodes();
        }

        if (!is_null($type)) {
            $code = $type . ':' .$code;
        }

        if (!isset(static::$values[$code])) {
            throw new RuntimeException("Iblock with code '{$code}' was not found in iblock_id()");
        }

        return static::$values[$code];
    }
    
    /**
     * Получение ID всех инфоблоков из БД/кэша.
     *
     * @return array
     */
    public static function getAllByCodes()
    {
        $callback = function() {
            $iblocks = [];

            $sql = 'SELECT ID, CODE, IBLOCK_TYPE_ID FROM b_iblock WHERE CODE != ""';
            $dbRes = Application::getConnection()->query($sql);
            while ($i = $dbRes->fetch()) {
                $id = (int) $i['ID'];
                $iblocks[$i['CODE']] = $id;
                $iblocks[$i['IBLOCK_TYPE_ID'].':'.$i['CODE']] = $id;
            }

            return $iblocks;
        };

        return static::$cacheMinutes ? Cache::remember('arrilot_bih_iblock_ids', static::$cacheMinutes, $callback) : $callback();
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
}
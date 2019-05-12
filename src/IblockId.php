<?php

namespace Arrilot\BitrixIblockHelper;

use Arrilot\BitrixCacher\Cache;
use Bitrix\Main\Application;
use RuntimeException;

class IblockId
{
    use Cacheable;

    /**
     * Директория где хранится кэш.
     *
     * @return string
     */
    protected static function getCacheDir()
    {
        return '/arrilot_bih_iblock_id';
    }

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
            throw new RuntimeException("Iblock with code '{$code}' was not found");
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

        return static::$cacheTtl
            ? Cache::remember('arrilot_bih_iblock_ids', static::$cacheTtl, $callback, static::getCacheDir())
            : $callback();
    }
}

<?php

namespace Arrilot\BitrixIblockHelper;

use Arrilot\BitrixCacher\Cache;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use RuntimeException;

class HLblock
{
    use Cacheable;

    /**
     * Хранилище скомпилированных сущностей для хайлоадблоков.
     *
     * @var array
     */
    protected static $compiledEntities = [];

    /**
     * Директория где хранится кэш.
     *
     * @return string
     */
    protected static function getCacheDir()
    {
        return '/arrilot_bih_hlblock';
    }

    /**
     * Получение данных хайлоадблока по названию его таблицы.
     * Всегда выполняет лишь один запрос в БД на скрипт и возвращает массив вида:
     *
     * array:3 [
     *   "ID" => "2"
     *   "NAME" => "Subscribers"
     *   "TABLE_NAME" => "app_subscribers"
     * ]
     *
     * @param string $table
     * @return array
     */
    public static function getByTableName($table)
    {
        if (is_null(static::$values)) {
            static::$values = static::getAllByTableNames();
        }

        if (!isset(static::$values[$table])) {
            throw new RuntimeException("HLBlock for table '{$table}' was not found");
        }

        return static::$values[$table];
    }

    /**
     * Получение ID всех инфоблоков из БД/кэша.
     *
     * @return array
     */
    public static function getAllByTableNames()
    {
        $callback = function() {
            $hlBlocks = [];

            $sql = 'SELECT `ID`, `NAME`, `TABLE_NAME` FROM b_hlblock_entity';
            $dbRes = Application::getConnection()->query($sql);
            while ($block = $dbRes->fetch()) {
                $hlBlocks[$block['TABLE_NAME']] = $block;
            }

            return $hlBlocks;
        };

        return static::$cacheMinutes
            ? Cache::remember('arrilot_bih_hlblocks', static::$cacheMinutes, $callback, static::getCacheDir())
            : $callback();
    }
    
    /**
     * Компилирование и возвращение класса для хайлоадблока для таблицы $table.
     *
     * Пример для таблицы `app_subscribers`:
     * $subscribers = \Arrilot\BitrixIblockHelper\HLblock::compileClass('app_subscribers');
     * $subscribers::getList();
     *
     * @param string $table
     * @return string
     */
    public static function compileClass($table)
    {
        $hldata = static::getByTableName($table);
        static::compileEntity($table);

        return $hldata['NAME'] . 'Table';
    }

    /**
     * Компилирование сущности для хайлоадблока для таблицы $table.
     * Выполняется один раз.
     *
     * Пример для таблицы `app_subscribers`:
     * $entity = \Arrilot\BitrixIblockHelper\HLblock::compileEntity('app_subscribers');
     * $query = new Entity\Query($entity);
     *
     * @param string $table
     * @return \Bitrix\Main\Entity\Base
     */
    public static function compileEntity($table)
    {
        if (!isset(static::$compiledEntities[$table])) {
            Loader::includeModule('highloadblock');
            static::$compiledEntities[$table] = HighloadBlockTable::compileEntity(static::getByTableName($table));
        }

        return static::$compiledEntities[$table];
    }
}

<?php

namespace Arrilot\BitrixIBlockHelper;

use Arrilot\BitrixCacher\Cache;
use Bitrix\Main\Application;

class IblockHelper
{
    public static function getIblockIdsByCodes($cacheMinutes = 0)
    {
        $callback = function() {
            $iblocks = [];

            $sql = 'SELECT ID, CODE, IBLOCK_TYPE_ID FROM b_iblock WHERE CODE != ""';
            $dbRes = Application::getConnection()->query($sql);
            while ($i = $dbRes->fetch()) {
                $iblocks[$i['CODE']] = $i['ID'];
                $iblocks[$i['IBLOCK_TYPE_ID'].':'.$i['CODE']] = $i['ID'];
            }

            return $iblocks;
        };

        return $cacheMinutes ? Cache::remember('arrilot_bih_iblock_ids', $cacheMinutes, $callback) : $callback;
    }
}
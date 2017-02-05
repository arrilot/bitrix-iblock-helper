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
                $id = (int) $i['ID'];
                $iblocks[$i['CODE']] = $id;
                $iblocks[$i['IBLOCK_TYPE_ID'].':'.$i['CODE']] = $id;
            }

            return $iblocks;
        };
        
        echo 1;

        return $cacheMinutes ? Cache::remember('arrilot_bih_iblock_ids', $cacheMinutes, $callback) : $callback();
    }
}
[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-iblock-helper/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-iblock-helper/)

# Вспомогательный класс для решения проблемы с ID инфоблоков.

## Установка

```composer require arrilot/bitrix-iblock-helper```

## Использование

Рекомендуемый способ использования - добавить в проект следующую функцию-хэлпер
```php
/**
 * Получение ID инфоблоку по коду (или по типу:коду).
 * Помогает вовремя обнаруживать опечатки.
 *
 * @param string $code
 * @return int
 *
 * @throws RuntimeException
 */
function iblock_id($code)
{
    // Запрашиваем данные из базы/кэша только при первом обращениию.
    static $iblocks = null;
    if (is_null($iblocks)) {
        $iblocks = Arrilot\BitrixIblockHelper\IblockHelper::getIblockIdsByCodes();
    }

    if (!isset($iblocks[$code])) {
        throw new RuntimeException("Iblock with code '{$code}' was not found in iblock_id()");
    }

    return $iblocks[$code];
}
```

Допустим, есть инфоблок типа `other` и с символьным кодом `articles`.

Его ID можно получить при помощи одного из вариантов:
 1. `$id = iblock_id('other:articles')` - строгий вариант
 2. `$id = iblock_id('articles')` - более удобный в случае когда коды инфоблоков можно считать уникальными.

В качестве аргумента `IblockHelper::getIblockIdsByCodes()` можно передать число.
Например, `IblockHelper::getIblockIdsByCodes(30)`
В этом случае результат выборки будет закэширован на указанное количество минут (30).
Может быть полезно при очень большом количестве инфоблоков.

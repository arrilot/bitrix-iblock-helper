[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-iblock-helper/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-iblock-helper/)

# Вспомогательный класс для решения проблемы с ID инфоблоков.

## Установка

```composer require arrilot/bitrix-iblock-helper```

## Использование

```php
// Один раз задаем глобальную константу-массив на этапе инициализации приложения.
define('IBLOCKS', Arrilot\BitrixIblockHelper\IblockHelper::getIblockIdsByCodes());

// Затем используем так:
$filter = ['IBLOCK_ID' => IBLOCKS['other:articles']];
// где `other` - тип инфоблока, `articles` - код.

// Или даже так, если код вы считаете уникальным в рамках всех типов инфоблоков:
$filter = ['IBLOCK_ID' => IBLOCKS['articles']];
```

В качестве аргумента `IblockHelper::getIblockIdsByCodes()` можно передать число.
В этом случае результат выборки будет закэширован на указанное количество минут.
Может быть полезно при очень большом количестве инфоблоков.

```php
// Кэшируем на 30 минут
define('IBLOCKS', Arrilot\BitrixIblockHelper\IblockHelper::getIblockIdsByCodes(30));
```

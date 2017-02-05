[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-iblock-helper/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-iblock-helper/)

# Вспомогательный класс для решения проблемы с ID инфоблоков.

## Установка

```composer require arrilot/bitrix-iblock-helper```

## Использование

Рекомендуемый способ использования - добавить в проект следующую функцию-хэлпер
```php
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
function iblock_id($code, $type = null)
{
    return Arrilot\BitrixIblockHelper\IblockId::getByCode($code, $type);
}
```

Допустим, есть инфоблок типа `other` и с символьным кодом `articles`.

Его ID можно получить при помощи одного из вариантов:
 1. `$id = iblock_id('articles', 'other')` - строгий вариант
 2. `$id = iblock_id('other:articles')` - тоже самое
 3. `$id = iblock_id('articles')` - более удобный в случае когда коды инфоблоков можно считать уникальными.

Независимо от количества вызовов `iblock_id()` запрос в базу будет выполнен только один раз за и получит данные по всем инфоблокам.

Если на проекте создано крайне много инфоблоков, то можно закэшировать этот запрос добавив в `init.php`
```php
 Arrilot\BitrixIblockHelper\IblockId::setCacheTime(30); // кэшируем ID всех инфоблоков на 30 минут
```
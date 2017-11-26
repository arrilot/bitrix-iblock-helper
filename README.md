[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-iblock-helper/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-iblock-helper/)

# Хэлперы для упрощения работы с инфоблоками/хайлоадблоками

Данный пакет представляет собой пару классов которые позволяют удобно и производительно получать

1. Идентификаторы инфоблоков по их символьным кодам
2. Различную информацию о хайлоадблоках по названию таблицы

Производительность достигается за счёт того, что мы не запрашиваем из БД данные каждый раз когда вызывается какой-либо из методов
Вместо этого данные получаются из БД один раз и сразу по всем инфоблокам/хайлоадблокам и опционально могут еще и кэшироваться

## Установка

```composer require arrilot/bitrix-iblock-helper```

## Использование

### Инфоблоки

Рекомендуемый способ использования - добавить в проект следующую функцию-хэлпер:

```php
/**
 * Получение ID инфоблока по коду (или по коду и типу).
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

### Хайлоадблоки

Рекомендуемый способ использования - добавить в проект следующие функции-хэлперы:

```php
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
function highloadblock($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::getByTableName($table);
}

/**
 * Компилирование и возвращение класса для хайлоадблока для таблицы $table.
 *
 * Пример для таблицы `app_subscribers`:
 * $subscribers = highloadblock_class('app_subscribers');
 * $subscribers::getList();
 *
 * @param string $table
 * @return string
 */
function highloadblock_class($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::compileClass($table);
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
function highloadblock_entity($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::compileEntity($table);
}
```

### Кэширование

Все запросы генерируемые пакетом можно закэшировать, что может быть полезным, например, если на проекте есть очень много инфоблоков.

Для этого следует добавит в `init.php` (или куда-то туда):
```php
 Arrilot\BitrixIblockHelper\IblockId::setCacheTime(30); // кэшируем ID всех инфоблоков на 30 минут
 Arrilot\BitrixIblockHelper\HLblock::setCacheTime(30); // кэшируем данные всех хайлоадблоков на 30 минут
```

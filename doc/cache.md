
##Кэширование

###Основы

Для хранения данных в кэше Knee предоставляет класс `Cache`.

Для манипуляции с кэшем используется memory cache daemon **memcached**, поэтому для работы c классом `Cache` memcached должен быть установлен в системе и подключен один из PHP драйверов memcache или memcached.

Приступая к работе с кэшем, нужно настроить некоторые параметры, которые находятся в файле конфигурации `/app/configs/cache.php`.

* **driver** — PHP драйвер для работы с демоном memcached. Может принимать значения memcache или memcached</li>
* **token** — ключ-прификс, который используется для хранения данных в memcached. Он требуется для предотвращения случайного доступа к данным другими программами, работающими с memcached. По умолчанию имеет значение "knee__" и его можно не изменять;</li>
* **servers** — массив с данными для подключения к хранилищам memcached.
  * **host** — IP хоста для подключения. По умолчанию имеет значение "127.0.0.1";
  * **port** — Номер порта для подключения. По умолчанию имеет значение "11211";
  * **weight** — Вес сервера. Чем он больше, ем больше вероятности, что для хранения данных будет выбран он. По умолчанию имеет значение "100".

###Доступ

Класс `Cache` имеет ряд полезных методов для проведения манипуляций с кэшем.

####Запись данных в кэш

```php
Cache::set('name', 'Jack');
Cache::set('name', 'Jack', ['tag1', 'tag2']);
Cache::set('city', 'Casablanca', ['tag1', 'tag2'], '1 day');
```

Третий параметр может задавать теги для значения в кэше. Используя теги можно обнулять группу кэшей используя метод `clear`.

Четвертый параметр задает время действия значения в кэше. Указание времени осуществляется строкой, которая может быть нескольких видов (N sec, N min, N hour, N day, N month, N year), где N это число.

####Получение данных из кэша

```php
Cache::get('name');
```

В случае, если данных с таким ключом не существует, метод вернет `null`.

####Удаление данных

```php
Cache::del('name');
```

####Очистка кэша

```php
Cache::clear();
Cache::clear('tag1');
```

Необязательный параметр является именем тега и позволяет обнулить группу помеченную этим тегом.
Установка и обнуление тегов у кэшей удобно для установления зависимостей между различными данными. 
Если одни данные устарели, можно быстро обнулить кэши с которыми эти данные тесно взаимодействуют. 

На самом деле memcached не удаляет данные, а делает их недоступными.

####Проверка на существование

```php
Cache::exists('name');
```

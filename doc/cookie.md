
##Cookies

###Основы

Для работы с файлами Cookies Knee предоставляет класс `Cookie`.

Одной из особенностей класса является, что данные куков можно получить сразу же после их установки.

###Доступ

Класс `Cookie` имеет ряд полезных методов для работы с файлами Cookies.

####Установка Cookies

```php
Cookie::set('name', 'Jack', '1 month');
Cookie::set('city', 'Casablanca', '1 day', '/', '.example.com', true);
```

Набор параметров метода `set` идентичен набору параметров функции `setcookie`.
Установка времени действия Cookie осуществляется строкой, которая может быть нескольких видов (N sec, N min, N hour, N day, N month, N year), где N это число.

####Удаление Cookies

```php
Cookie::del('name');
Cookie::del('city', '/', '.example.com');
```

####Получение значения Cookies

```php
Cookie::get('name');
Cookie::get('city');
```

В случает, если кук не существует, метод вернет `null`

####Проверка на существование Cookies

```php
Cookie::exists('name');
Cookie::exists('city');
```

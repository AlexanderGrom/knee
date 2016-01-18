
##Сообщения

###Основы

Обычно при проверке пользовательского ввода, разработчик устанавливает некоторые сообщения об ошибках для отдачи конечному пользователю. Для помощи в этом деле предлагается использовать класс `Message`, который обладает удобным функционалом для буферизации сообщений.

###Доступ

Класс `Message` имеет ряд полезных методов.

####Создание нового контейнера для сообщений

```php
Message::start();
```

####Закрытие контейнера для сообщений

```php
Message::end();
```

####Запить нового сообщения

```php
Message::set('Сообщение');
```

####Получение сообщений в виде текста

```php
Message::getText();
```

####Получение сообщений в виде массива

```php
Message::getList();
```

####Получение сообщений в виде данных в формате JSON

```php
Message::getJSON();
```

####Получение сообщений в виде данных в формате HTML

```php
Message::getHTML();
```

####Получение количества сообщений в контейнере

```php
Message::count();
```

####Получение уровня вложености контейнера

```php
Message::level();
```

####Очистка контейнера

```php
Message::clear();
```

####Группировка сообщений

```php
Message::group('error')->set('Сообщение');
Message::group('error')->getText();
```

При группировки доступны те же методы, что и без неё.

###Пример

```php
Message::start();

Message::set('Сообщение 1');
Message::set('Сообщение 2');
Message::set('Сообщение 3');

Message::count(); // 3

echo Message::getHTML();

Message::start();
Message::level(); // 2
Message::count(); // 0
Message::end();

Message::end();

Message::group('error')->start();
Message::group('error')->set('Сообщение 1');
Message::group('error')->set('Сообщение 2');

Message::group('info')->start();
Message::group('info')->set('Сообщение 1');
Message::group('info')->set('Сообщение 2');

echo Message::group('error')->getHTML();
echo Message::group('info')->getHTML();
```
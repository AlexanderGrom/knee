
##Почта

###Основы

Для отправки писем электронной почты Knee предоставляет класс `Mail`.

В Класс `Mail` может опралять письма в текстовом и html формате. 

Одной из особенностей класса является стабильная работа с символами юникода. 
Помимо обработки тела сообщения, класс обрабатывает заголовки, которые этого нуждаются, что нельзя сказать о php функции `mb_send_mail`.

Шаблоны писем хранятся в директории `/app/views/mail/` и обрабатываются шаблонизатором класса `View`.

###Доступ

Класс `Mail` имеет простой интерфейс `Mail::maker`.

```php
$mailText = View::make('mail.mymail')->compile();

Mail::maker()
->to('jack@example.com')
->from('mike@example.com', 'Mike')
->subject('Hello Jack')
->text($mailText)
->send();
```

####Адрес и имя получателя

```php
$mailMaker = Mail::maker()->to('jack@example.com');
$mailMaker = Mail::maker()->to('jack@example.com', 'Jack');
```

Можно вызвать несколько методов `to` в цепочке для задания нескольких получателей.
Не рекомендуется использовать для массовой рассылки.

####Адрес и имя отправителя

```php
$mailMaker = Mail::maker()->from('mike@example.com');
$mailMaker = Mail::maker()->from('mike@example.com', 'Mike');
```

####Заголовок

```php
$mailMaker = Mail::maker()->subject('Заголовок письма');
```

####Тело

```php
$mailMaker = Mail::maker()->text('Текстовое тело письма');
$mailMaker = Mail::maker()->html('Тело письма в формате html');
```

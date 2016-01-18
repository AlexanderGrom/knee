

##Строки

###Основы

Для работы со строками Knee предоставляет класс `Str`.

###Доступ

####Лимит на число пробельных символов в строке

```php
Str::limit_space($text, $limit);
```

####Лимит на число переводов строки

```php
Str::limit_nr($text, $limit);
```

####Лимит на число символов в строке

```php
$text = "Очень длинный текст сообщения";
Str::limit_char($text, 17); // Очень длинный тек...
Str::limit_char($text, 17, false); // Очень длинный...
Str::limit_char($text, 17, false, false); // Очень длинный
```

####Поиск в строке по "звёздочке"

```php
$text = "Очень длинный текст сообщения";
Str::match('Очень *', $text); // true
```

####Транслирует символ перевода строки в тег 

```php
Str::nr2br($text);
```

####Транслирует тег  в символ перевода строки

```php
Str::br2nr($text);
```

####Транслирует спец символы &"><' в десятичное ASCII представление

```php
Str::special($text);
```

####Генерация случайной строки заданной длины

```php
Str::hash(32);
```

####Подсвечивает текстовые ссылки в строке

```php
Str::highlight_link($text);
```

####Цифровая строка

```php
Str::digit(); // 0123456789
```

####Алфавитная строка

```php
Str::alpha(); // abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
```

####Алфавитно-цифровая строка

```php
Str::alnum(); // abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789
```

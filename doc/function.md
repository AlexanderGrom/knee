
##Функции ядра

###Основы

Knee содержит некоторое количество дополнительных функций в помощь разработчику.

Все дополнительные функции для приложения следует складывать в директорию `/app/function/`.
По умолчанию в ней находится файл `main.php`, который уже подключен в `/app/loaders.php`.

###Доступ

####Информация о переменной

```php
test($array);
test($array, true);
```

####Проверяет являются ли значения валидными положительными числами

```php
Route::get('/blog/<#id>/{page<#page>}?/', function($id, $page = 1)
{		
if( ! is_numbers($id, $page)) Error::e404();
});
```

Например, числа 000, 01, 001.543 НЕ являются валидными

####Удаляет элементы массива с указанными значениями

```php
$array = array(1,2,3,4,5,6,7);
$new_array = array_delete($array, $array(1,2,5)); // 4,6,7
$new_array = array_delete($array, 7); // 1,2,3,4,5,6
```

####Удаляет элементы массива с указанными ключами

```php
$array = array(1,2,3,4,5,6,7);
$new_array = array_delete_key($array, $array(0,1,4)); // 4,6,7
$new_array = array_delete_key($array, 6); // 1,2,3,4,5,6
```

####Добавляет элементы в массив в указанную позицию

```php
$array = array(1,2,3,4,5,6,7);
array_insert($array, 3, array(9,9,9)); // вставить буквально на третье место
$array; // 1,2,9,9,9,3,4,5,6,7
```

####Произведет implode многомерного массива

```php
array_multi_implode(',', $array);
```

####Проверка, что хоть один элемент массива подходят под значение

```php
$array = array(false, false, true, false);
array_any('true', $array); // true
```

####Проверка, что все элементы массива подходят под значение

```php
$array = array(false, false, true, false);
array_all('false', $array); // false
```

####Многобайтные строки — mb_ucfirst

```php
mb_ucfirst('заголовок'); // Заголовок
mb_ucfirst('заголовок', 'UTF-8'); // Заголовок
```

####Многобайтные строки — mb_lcfirst

```php
mb_lcfirst('ЗАГОЛОВОК'); // зАГОЛОВОК
mb_lcfirst('ЗАГОЛОВОК', 'UTF-8'); // зАГОЛОВОК
```

####Многобайтные строки — mb_ucwords

```php
mb_ucwords('ЗАГОЛОВОК TEST'); // Заголовок Test
mb_ucwords('ЗАГОЛОВОК TEST', 'UTF-8'); // Заголовок Test
```

####Многобайтные строки — mb_substr_replace

```php
mb_substr_replace('Заголовок', 'товок', 4); // Заготовок
mb_substr_replace('Заголовок', 'товок', 4, null, 'UTF-8'); // Заготовок
```

####Многобайтные строки — mb_chunk_split

```php
mb_chunk_split($text);
mb_chunk_split($text, 76, "\r\n");
```

####Многобайтные строки — mb_str_shuffle

```php
mb_str_shuffle('заголовок'); // овкаологз
mb_str_shuffle('заголовок', 'UTF-8'); // овкаологз
```

####Многобайтные строки — mb_ord

```php
mb_ord('Ж'); // 1046
mb_ord('Ж', 'UTF-8'); // 1046
```

####Многобайтные строки — mb_chr

```php
mb_chr(1046); // Ж
mb_chr(1046, 'UTF-8'); // Ж
```

####Многобайтные строки — mb_preg_match_all

```php
mb_preg_match_all();
```

Вообще функции preg_match и preg_match_all нормально работают с юникодом, но если требуется получить позицию найденного вхождения в строку и задать позицию поиска, то могут возникать проблемы.

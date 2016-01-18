
##Формы

###Основы

Часто требуется динамически управлять элементами HTML-форм на странице, в частности подгрузка и установка значений по умолчанию.

Обычно проблемы возникают при формировании списков `<select>` или установки активности флажков `checkbox` и переключателей `radio`. Для упрощения этой задачи Knee Framework предоставляет класс `From`.

###Доступ

Рассмотрим доступный функционал класса `Form`. Параметры методов представляют собой массив атрибутов HTML-элементов формы.

####Создание формы

```php
Form::open(['action'=>"", 'method'=>"post", 'name'=>"my_form"]);
// HTML
Form::close();
```

####Текстовое поле

```php
Form::text(['name'=>"my_text", 'value'=>"", 'id'=>"id_text", 'class'=>"my_class"]);
```

####Текстарея

```php
Form::textarea(['name'=>"my_textarea", 'value'=>"", 'class'=>"my_class"]);
```

####Поле с указанием пароля
```php
Form::password(['name'=>"my_password", 'value'=>"", 'class'=>"my_class"]);
```

####Скрытое текстовое поле

```php
Form::hidden(['name'=>"my_hidden", 'value'=>"", 'class'=>"my_class"]);
```

####Поле выбора файла

```php
Form::file(['name'=>"my_file", 'id'=>"id_file", 'class'=>"my_class"]);
```

####Флажки checkbox

```php
Form::checkbox(['name'=>"my_checkbox_1", 'value'=>"1", 'checked'=>true]);
Form::checkbox(['name'=>"my_checkbox_2", 'value'=>"2", 'checked'=>false]);
Form::checkbox(['name'=>"my_checkbox_3", 'value'=>"3", 'checked'=>true]);

```

####Переключатели radio

```php
Form::radio(['name'=>"my_radio", 'value'=>"1", 'checked'=>true]);
Form::radio(['name'=>"my_radio", 'value'=>"2", 'checked'=>false]);
Form::radio(['name'=>"my_radio", 'value'=>"3", 'checked'=>true]);

```

####Списоки select

```php
$options = array();
$options['1'] = "red";
$options['2'] = "blue";
$options['3'] = "green";

Form::select(['name'=>"my_select", 'options'=>$options, 'selected'=>"2"]);

//-----

$group_1 = array();
$group_1['1'] = "red";
$group_1['2'] = "blue";
$group_1['3'] = "green";

$group_2 = array();
$group_2['4'] = "black";
$group_2['5'] = "white";

$options = array('Group #1' => $group_1, 'Group #2' => $group_2);

Form::select(['name'=>"my_select", 'options'=>$options, 'selected'=>"2"]);
```

####Кнопки submit, button и reset

```php
Form::submit(['name'=>"my_submit", 'value'=>"Submit", 'class'=>"my_class"]);
Form::button(['name'=>"my_button", 'value'=>"Button", 'class'=>"my_class"]);
Form::reset(['name'=>"my_reset", 'value'=>"Reset", 'class'=>"my_class"]);
```

####Лейбл

```php
Form::text(['name'=>"my_text", 'value'=>"", 'id'=>"id_text", 'class'=>"my_class"]);
Form::label(['for'=>"id_text", 'value'=>"It`s text"]);
```

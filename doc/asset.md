
##Ресурсы и мета-описание

###Основы

Обычно в веб-приложениях принято динамически подключать пользовательские стили CSS, JavaScript скрипты и составлять мета-описание страницы.
Для упрощения этой задачи Knee предоставляет класс `Asset`, с помощью которого можно подключить абсолютно любой ресурс, загружаемый посредствам тега `<link>`, скриптовый файл подключаемый тегом `<script>` или мета-описание представленное тегом `<meta>`.

###Доступ

Установка, добавление или выборка нужных ресурсов осуществляется путем передачи массива атрибутов тега в соответствующие методы класса `Asset`.

####Добавление

```php
Asset::setLink(['href'=>"/css/common.css", 'rel'=>"stylesheet", 'type'=>"text/css", 'media'=>"all"]);
Asset::setLink(['href'=>"/css/post_show.css", 'rel'=>"stylesheet", 'type'=>"text/css", 'media'=>"all"]);
Asset::setLink(['href'=>"/css/post_comment.css", 'rel'=>"stylesheet", 'type'=>"text/css", 'media'=>"all"]);

Asset::setScript(['src'=>"/js/common.js", 'type'=>"text/javascript", 'data-group'=>"head"]);
Asset::setScript(['src'=>"/js/post_comment.js", 'type'=>"text/javascript", 'data-group'=>"head"]);
Asset::setScript(['src'=>"/js/post_voice.js", 'type'=>"text/javascript", 'data-group'=>"head"]);

Asset::setMeta(['property'=>"og:image", 'content'=>"/img/logo.png"]);
Asset::setMeta(['property'=>"og:title", 'content'=>"Knee Framework / Главная страница"]);
Asset::setMeta(['property'=>"og:description", 'content'=>"Описание содержания страницы"]);
```

####Выборка и получение значений

```php
// Выбрать все теги link
Asset::getLink();

// Выбрать все стили css
Asset::setLink(['type'=>"text/css"]);

// Выбрать все стили css из папки /css/
Asset::setLink(['href'=>"/css/*", 'type'=>"text/css"]);

//-----

// Выбрать все скрипты
Asset::getScript();

// Выбрать все JavaScript скрипты
Asset::getScript(['type'=>"text/javascript"]);

// Выбрать все JavaScript скрипты из папки /js/
Asset::getScript(['href'=>"/js/*", 'type'=>"text/javascript"]);

// Выбрать все JavaScript скрипты с пометкой "head"
Asset::getScript(['type'=>"text/javascript", 'data-group'=>"head"]);

//-----

// Выбрать все мета-описание
Asset::getMeta();

// Выбрать описание изображений
Asset::getMeta(['property'=>"og:image"]);
```

####Удаление данных

```php
// Удалить все теги link
Asset::delLink();

// Удалить все стили css
Asset::delLink(['type'=>"text/css"]);

// Удалить все стили css из папки /css/
Asset::delLink(['href'=>"/css/*", 'type'=>"text/css"]);

//-----

// Удалить все скрипты
Asset::delScript();

// Удалить все JavaScript скрипты
Asset::delScript(['type'=>"text/javascript"]);

// Удалить все JavaScript скрипты из папки /js/
Asset::delScript(['href'=>"/js/*", 'type'=>"text/javascript"]);

// Удалить все JavaScript скрипты с пометкой "head"
Asset::delScript(['type'=>"text/javascript", 'data-group'=>"head"]);

//-----

// Удалить все мета-описание
Asset::delMeta();

// Удалить описание изображений
Asset::delMeta(['property'=>"og:image"]);
```

Интерфейс класса `Asset` достаточно универсальный, вы можете создать свои функции добавления ресурсов подходящие под конкретный проект, тем самым избавив себя от постоянного определения атрибутов по умолчанию.

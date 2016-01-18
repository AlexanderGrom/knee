
##Маршрутизация

###Основы

Описание маршрутизации вашего приложения должны находиться в файле `/app/route.php`. 
Маршрутизация состоит из указания URI, Анонимной Callback функции и фильтров маршрутов.
Вы можете использовать маршрутизацию для методов запроса **GET**, **POST**, **PUT**, **DELETE**, а так же **ANY** для всех методов запроса.

> **Важно:** Действия производятся в порядке их регистрации,  
поэтому всеядные роуты должны быть указаны после всех остальных.

####Регистрация маршрута на GET-запрос

```php
Route::get('/', function()
{		
	return "Hello World!";
});
```

####Регистрация маршрута на POST-запрос

```php
Route::post('/', function()
{		
	return "Hello World!";
});
```

####Регистрация маршрута на GET и POST запросы

```php
Route::any('/', function()
{		
	return "Hello World!";
});
```

##Шаблоны

> **Важно:** Имена шаблонов и имена параметров функции должны совпадать, совпадение порядка их следования не обязательно!

####Шаблон для любых символов

```php
Route::get('/user/<*name>/', function($name)
{		
	return "Hello ".$name;
});
```

####Шаблон для целых чисел

```php
Route::get('/post/<#id>/', function($id)
{		
	return "Post №".$id;
});
```

####Шаблон для символов латинского алфавита, цифр и тире

```php
Route::get('/page/<@name>/', function($name)
{		
	return "Page name - ".$name;
});
```

####Опциональные параметры

```php
Route::get('/blog/{page<#page>}?/', function($page = 1)
{		
	return "Page №".$page;
});
```

####Или то, или это
```php
// html или htm
Route::get('/archive/title.(html|htm)?', function()
{		
	return "Archive title";
});

// title.html или title-99.html
Route::get('/archive/title(-<#page>)?.html', function()
{		
	return "Archive title";
});
```

Так же получить значение параметра можно используя метод `Route::match('param_name')`. 
Данные берутся из параметров функции роута, так что их указание обязательно! 
Если параметр не получил значение и не имеет значения по умолчанию, то его значение будет `Null`.

##Фильтры

Часто бывает необходимо ограничить вызов того или иного маршрута, например если не была произведена авторизация или другие действия требующие выполнения.
Для этого Knee предлагает использовать фильтры маршрутов. Если фильтр возвращает `False`, то маршрут не выполняется.

```php
Route::filter('is_auth', function()
{
	// action
	return true;
});
```

```php
Route::get('/admin/', function()
{		
	return "Admin panel";
}, 'is_auth');
```

На одном маршруте можно разместить несколько фильтров передав их имена в виде массива `array('is_auth', 'is_allow')`. В этом случае, если хоть один фильтр вернет `false`, то маршрут не будет выполнен.

##Хосты

Вы можете явно указать имя хоста, который будет обрабатывать те или иные маршруты.

```php
Route::host('example.com', function()
{		
	Route::get('/', function()
	{		
		return "example.com";
	});
});
```

####Указание хоста с маской
```php
Route::host('*.example.com', function()
{		
	// routes
});
```

####Указание хоста с фильтром
```php
Route::host('admin.example.com', function()
{		
	// routes
}, 'is_auth');
```

##Группы

Если вы испытываете необходимость в частом использовании одного и того же набора фильтров, то маршруты имеет смысл сгруппировать.

```php
Route::group('*', function()
{		
	Route::get('/', function()
	{		
		return "Hello World";
	});

	Route::get('/admin/', function()
	{		
		return "Admin panel";
	});
}, 'is_auth');
```

```php
Route::group('/admin/*', function()
{			
	Route::get('/admin/', function()
	{		
		return "Admin panel";
	});

	Route::get('/admin/logout/', function()
	{		
		return "Admin logout";
	});
}, 'is_auth');
```

> **Примечание:** Во избежание создания дублей страниц роут не может иметь путь `/index.php`, `/public/`.


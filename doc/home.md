

##Введение

**Knee Framework** — это простая среда для быстрого создания веб-приложений на языке программирования PHP. 
Фреймворк имеет богатый базовый функционал, который нетрудно расширить под собственные нужды.

**Философия проекта** — простота. Простота, как в реализации самого фреймворка так и в написание на нем веб-приложений.
Используя простой синтаксис сложных действий, разработчик в прямом смысле может писать на коленке, как простые, так и сложные веб-приложения.

Вместе с простотой Knee Framework предоставляет гибкость в написании рутинного кода, не загоняя разработчика в жесткие рамки.
Вследствие этого, имеется возможность создавать не типовые проекты с особыми условиями и реализациями проблем.

##Немного кода

```php
Route::host('knee-faramework.dev', function()
{	
Route::filter('is_auth', function()
{
// authorization check
return true;
});

Route::get('/', function()
{		
return View::make('main.index')
->with('title' => 'Main Page');
->with('content' => 'Hello World!');
->with('footer' => 'Powered by Knee Framework');
});

Route::post('/page/{<*name>}?/', function($name)
{			
return Controller::get('page')->index($name);
});

Route::any('/admin/', function()
{			
return Controller::get('admin')->index();
}, 'is_auth');	
});
```


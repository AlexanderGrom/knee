

##Пагинация

###Основы

В большинстве веб-проектов требуется постраничная пагинация. Для избавления от математических вычислений Knee предоставляет класс пагинации `Page`.

Knee избавляет только от математических вычислений и генерации URI, все остальные действия при построении пагинации разработчик должен взять на себя.
Knee не знает, как разработчик захочет оформить свою пагинацию, и в каком виде её отобразит на странице.

Knee предлагает три способа передачи номера страницы:

1. Сегмент пути URI `/user/page2/`
2. GET параметр `/user/list.html?page=2`
3. В имени файла `/user/list-2.html`

###Доступ

Класс `Page` имеет один метод `make` который производит математические операции и возвращает  объект отвечающий за генерацию необходимых URI ссылок.

```php
Page::make($total_item, $view_item, $view_page, $current_page[, $limit_page = null]);
```

* **$total_item** — общее кол-во элементов;
* **$view_item** —  кол-во элементов отображающихся на странице;
* **$view_page** —  кол-во страниц отображающихся в навигации;
* **$current_page** —  номер текущей странице. Если передано положиельное число, то Page будет считать, что номер страницы передан из URL. Так что значени по умолчанию следует указывать Null;
* **$limit_page** —  лимит на кол-во обрабатываемых страниц (необязателен). К примеру, этим параметром можно задать максимальное число обрабатываемых страниц, например 100.

Для генерации URI существует три метода:

1. **path([$name=page])** — Сегмент пути URI
2. **query([$name=page])** — GET параметр
3. **file()** — Имя файла

####Пример с генерации методом path()

```php
$page = Page::make($users_count, 10, 5, $current_page)->path();
print_r($page);

Array
(
	[list] => Array
	(
		[0] => Array
		(
			[number] => 1
			[url] => '/user/'
		)
		[1] => Array
		(
			[number] => 2
			[url] => '/user/page2/'
		)
		[2] => Array
		(
			[number] => 3
			[url] => '/user/page3/'
		)
		[3] => Array
		(
			[number] => 4
			[url] => '/user/page4/'
		)
		[4] => Array
		(
			[number] => 5
			[url] => '/user/page5/'
		)
	)

	[start] => 10
	[step] => 10
	[total] => 25
	[exists] => 1
	[prev] => 1
	[current] => 2
	[next] => 3
	[prev_url] => '/user/'
	[current_url] => '/user/page2/'
	[next_url] => '/user/page3/'
	[first_url] => '/user/'
	[last_url] => '/user/page5/'
)
```


* **list** — массив с номерами видимых страниц и uri на них;
* **start** — число записей, которые нужно пропустить при выборке;
* **step** — число записей, которые нужно выбрать для текущей странице;
* **total** — общее кол-во доступных страниц;
* **exists** — если параметр current_page является целым положительным числом, то Page будет считать, что параметр в URL существует;
* **prev** — номер предыдущей страницы;
* **current** — номер текущей страницы;
* **next** — номер следующей страницы;
* **prev_url** — uri предыдущей страницы;
* **current_url** — uri текущей страницы;
* **next_url** — uri следующей страницы;
* **first_url** — uri первой страницы;
* **last_url** — uri последней страницы.

####Простейший пример классической пагинации (шаблон)

```php
<div class="navigation">
	<div class="navigation_page">
		<div class="navigation_label">страницы:</div>
		<div class="navigation_list">
			<ul>
				{% foreach( $paging['list'] as $page ) %}
					{% if( $paging['current'] == $page['number'] ) %}
					<li class="navigation_list_selected">{% $page['number'] %}</li>
					{% else %}
					<li><a href="{% $page['url'] %}">{% $page['number'] %}</a></li>
					{% endif %}
				{% endforeach %}
			</ul>
		</div>
		<div class="navigation_count">[ {% $paging['total'] %} ]</div>
	</div>
	<div class="navigation_arrow">
		<div class="navigation_prev">
			{% if( $paging['current'] > 1 ) %}
			<a href="{% $paging['prev_url'] %}" rel="nofollow">← Сюда</a>
			{% else %}
			← Сюда
			{% endif %}
		</div>
		<div class="navigation_next">
			{% if( $paging['current'] < $paging['total'] ) %}
			<a href="{% $paging['next_url'] %}" rel="nofollow">Туда →</a>
			{% else %}
			Туда →
			{% endif %}
		</div>
	</div>
</div>
```

####Простейший пример классической пагинации (логика)

```php
Route::get('/blog/{page<#page>}?/', function($page)
{
	if (!is_numbers($page)) Error::e404();

	$posts_count = DB::query("SELECT COUNT(*) as count FROM posts")->fetch_object()->count;

	$paging = Page::make($posts_count, 10, 5, $page)->path();

	$DBData = array();
	$DBData['start'] = $paging['start'];
	$DBData['step'] = $paging['step'];

	$result = DB::query("SELECT * FROM posts LIMIT :start, :step", $DBData)->fetch_array_all();

	foreach ($result as $res) {
		// action
	}

	return View::make('main.blog.post')
		->with('paging', $paging);
});
```

Для простоты объяснения работа происходит непосредственно в роуте.


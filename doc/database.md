
##База данных

###Основы

Для работы базой данных Knee предоставляет класс `DB`.

В настоящее время Knee работает только с СУБД MySQL.
Работа с базой данных основана на PDO.

Перед началом работы необходимо произвести некоторые настройки подключения. Конфиг работы с базой данных хранится в `/app/configs/database.php`.

Настройка `error` определяет нужно ли отображать нюансы возникновения ошибок. Вторая настройка `connections` задает параметры подключения к базе данных.
По умолчанию используется соединение `default`, другие соединения могут быть использованы при явном их указании.

##SQL запросы

Все запросы к базе данных выполняются в режиме подготовленных выражений с некоторыми улучшениями. 
Например, использовать `:placeholders` теперь можно в конструкциях `ORDER BY` или `LIMIT`, 

####Выполнение запроса и получение записей из набора

```php
$comments = DB::query("SELECT * FROM comments WHERE topic_id=:id", array('id'=>10));

while ($res = $comments->getArray())
{
	echo $res['user_id'];
}
```

####Получить весь набор

```php
$comments = DB::query("SELECT * FROM comments WHERE topic_id=:id", array('id'=>10))->getArrayAll();

foreach (comments as $res)
{
	echo $res['user_id'];
}
```

####Получить данные в виде объекта

```php
$comment = DB::query("SELECT * FROM comments WHERE id=:id", array('id'=>99))->getObject();

echo $comment->title;

//-----

$count = DB::query("SELECT COUNT(*) as count FROM comments")->getObject()->count;

echo $count;

//-----

$comments = DB::query("SELECT * FROM comments WHERE topic_id=:id", array('id'=>10))->getObjectAll();

foreach(comments as $res)
{
	echo $res->title;
}
```

####Получить данные в виде списка

```php
$comments = DB::query("SELECT * FROM comments WHERE topic_id=:id", array('id'=>10))->getListAll();

foreach(comments as $res)
{
	echo $res[0];
}
```

####Получение к другой базе данных

```php
$comments = DB::connection('base')->query("SELECT * FROM comments WHERE topic_id=:id", array('id'=>10))->getArray();
```

####Получение кол-ва строк затронутых запросом

```php
$update = DB::->query("UPDATE comments SET add_date=:date", array('date'=>Time::now()));
echo $update->rowCount();
```

####Получение ID последней вставленной строки

```php
$insert = DB::query("INSERT INTO comments
(topic_id, user_id, text, add_date) 
VALUES 
(:topic_id, :user_id, :text, :add_date)", $data);

echo $insert->lastInsertId();
```

##Конструктор запросов

Помимо традиционных SQL запросов Knee предлагает конструктор запросов. 
Удобно использовать для простых запросов не захламляя модели.

####Несколько примеров

```php
DB::table('tags')
->where('tag_name', '=', 'javascript')
->distinct()
->count('post_id');
```

```php
DB::table('tags as tg', 'posts as ps')
->where('tg.tag_name', '=', 'php')
->where('tg.post_id', '=', DB::Raw('ps.post_id'))
->where('ps.post_status', '=', 'active')
->order('post_add_date', 'desc')
->limit(10)
->select();
```

```php
DB::table('user')
->where('user_id', '=', 1)
->update(['user_firstname'=>"John", 'user_lastname'=>"Сarrot"]);
```

```php
DB::table('user')
->where('follower', '=', 1)
->delete();
```

```php
DB::table('user')
->insert(['user_id'=>1, 'user_pid'=>"a"]);
```

```php
DB::table('user')
->insert([
	['user_id'=>1, 'user_pid'=>"a"],
	['user_id'=>2, 'user_pid'=>"b"]
]);
```

А так же:

```php
Or Where
->orWhere('user_id', '=', 1)
And Where
->andWhere('user_id', '=', 1)
```

```php
//группировка Where
->orWhere(function($builder){
    $builder->where('user_id', '=', 1)
            ->where('user_name', '=', 'John')
})
```

```php
//GroupBy
->group('user_id')
->group(['user_id','user_id'])
->group('user_id', 'user_id')
```

```php
//Having
->having('user_id', '<', '1000')
```

```php
//ругулятор возвращаемый полей (SELECT `user_id`, `user_name` FROM ...)
->select('user_id', 'user_name');
```

... и многое другое... см. исходные коды...

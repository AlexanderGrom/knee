
##Мапы

###Основы

Для того что бы скрыть описание тяжелых запросов к базе данных используется механизм мапинга.

Например, для работы с данными комментариев требуется много запросов к базе данных, такие как добавление, удаление, изменение данных, а так же различные варианты выборки.
Такие запросы могут быть достаточно громоздкими, по этому, что бы минимизировать код моделей и контролеров их лучше хранить в мапах.

Мапы должны храниться в директории `/app/maps/`, директория может иметь вложенные папки.

Доступ к мапам осуществляется "точечным путем" `dir.subdir.name`, а имя класса такого блока должно иметь вид `Dir_Subdir_Name_Map`.

В директории `/app/maps/` могут быть приватные папки имя которых начинается с "_" - нижние подчеркивание. Такие папки недоступны по точечному пути и могут хранить вспомогательные классы мапов. 

###Доступ

Предположим, у нас имеется мап `comments.php` который расположен в директории `/app/map/blog/`.
К такому мапу можно обратиться, используя путь `blog.comments`, и имя его класса должно иметь вид `Blog_Comments_Map`.

```php
class Blog_Comments_Map
{		
	public function addComment($data)
	{		
		DB::query("INSERT INTO comments
		(topic_id,
		 user_id,
		 text,
		 add_date
		) VALUES 
		(:topic_id,
		 :user_id,
		 :text,
		 :add_date
		)", $data);
	}

	public function delComment($comment_id)
	{		
		DB::query("DELETE FROM comments WHERE comment_id=:comment_id", array('comment_id'=>$comment_id));
	}

	public function getComments($topic_id, $start, $step)
	{		
		$DBData = compact('topic_id', 'start', 'step');

		return DB::query("SELECT * FROM comments WHERE topic_id=:topic_id ORDER BY add_date DESC LIMIT $start, $step", $DBData)->fetch_array();
	}
}
```

Теперь мы можем обратиться к методам этого мапа.

```php
Map::get('blog.comments')->getComments();
```

<?php
/*
 * Knee framework
 * Назначение: Работа с базой данный (Конструктор запросов)
 */

namespace Knee\DataBase;

use Closure;

class Builder
{
	/**
	 * Объект Query
	 */
	private $dbq = null;

	/**
	 * Тип запроса(select/insert/update/delete)
	 */
	public $type = null;

	/**
	 * Таблица с которой работаем
	 */
	public $table = null;

	/**
	 * Требуются ли только уникальные значения
	 */
	public $distinct = false;

	/**
	 * Составляем карту значений для плейсехолдеров
	 */
	public $bindings = array(
		'values' => [],
		'set' => [],
		'where' => [],
		'having' => [],
		'limit' => []
	);

	/**
	 * Карта значений для плейсехолдеров в зависимости от типа запроса
	 */
	public $bindings_map = array(
		'aggregate' => ['where'],
		'select' 	=> ['where','having','limit'],
		'insert' 	=> ['values'],
		'update' 	=> ['set','where','limit'],
		'delete'	=> ['where','limit']
	);

	/**
	 * Компоненты запроса
	 */
	public $components = array(
		'aggregate' => [],
		'select' => [],
		'insert' => [],
		'update' => [],
		'delete' => [],
		'from' => [],
		'into' => [],
		'columns' => [],
		'values' => [],
		'set' => [],
		'where' => [],
		'group' => [],
		'having' => [],
		'order' => [],
		'limit' => []
	);

	/**
	 * Карта компиляции компонентов
	 */
	 public $components_map = array(
		'aggregate' => ['aggregate','from','where'],
		'select' 	=> ['select','from','where','group','having','order','limit'],
		'insert' 	=> ['insert','into','columns','values'],
		'update' 	=> ['update','set','where','limit'],
		'delete' 	=> ['delete','from','where','limit']
	);

	/**
	 * Доступные операторы
	 */
	private $operators = array(
		'=', '!=', '<', '>', '<=', '>=', '<>',
		'IN', 'NOT IN',
		'LIKE', 'NOT LIKE',
		'&', '|', '^', '<<', '>>',
		'RLIKE', 'REGEXP', 'NOT REGEXP',
	);

	/**
	 * Конструктор
	 */
	public function __construct($dbq)
	{
		$this->dbq = $dbq;
	}

	/**
	 * Добавляет select
	 */
	public function select($columns = array('*'))
	{
		$this->type = 'select';

		$this->components['select'] = is_array($columns) ? $columns : func_get_args();
		$this->components['from'] = $this->table;

		$result = $this->exec();

		return $result;
	}

	/**
	 * Добавляет таблицу
	 */
	public function table($name)
	{
		$this->table = is_array($name) ? $name : func_get_args();

		return $this;
	}

	/**
	 * Добавляет where
	 */
	public function where($column, $operator = null, $value = null, $boolean = 'AND')
	{
		if (func_num_args() == 2) {
			list($operator, $value) = array('=', $operator);
		}

		if (count($this->components['where']) == 0) {
			$boolean = '';
		}

		if ($column instanceof Closure) {
			return $this->whereGroup($column, $boolean);
		}

		if (is_null($value)) {
			return $this->whereNull($column, $boolean, ($operator != '='));
		}

		$operator = strtoupper($operator);

		if (!in_array($operator, $this->operators)) {
			return $this;
		}

		if ($operator == 'IN') {
			return $this->whereIn($column, $value, $boolean);
		}
		else if ($operator == 'NOT IN') {
			return $this->whereNotIn($column, $value, $boolean, true);
		}

		$type = 'base';

		$this->components['where'][] = compact('type', 'column', 'operator', 'value', 'boolean');

		if (!($value instanceof Expression)) {
			$this->bindings['where'][] = $value;
		}

		return $this;
	}

	public function andWhere($column, $operator = null, $value = null)
	{
		$this->where($column, $operator, $value, 'AND');

		return $this;
	}

	public function orWhere($column, $operator = null, $value = null)
	{
		$this->where($column, $operator, $value, 'OR');

		return $this;
	}

	private function whereGroup(Closure $callback, $boolean = 'AND')
	{
		$builder = (new Builder($this->dbq))->table($this->table);

		call_user_func($callback, $builder);

		if (count($builder->components['where']) > 0) {
			$type = 'group';

			$this->components['where'][] = compact('type', 'builder', 'boolean');

			$this->bindings['where'] = array_merge($this->bindings['where'], $builder->bindings['where']);
		}

		return $this;
	}

	private function whereNull($column, $boolean = 'AND', $not = false)
	{
		$type = ($not) ? 'notNull' : 'Null';

		$this->components['where'][] = compact('type', 'column', 'boolean');

		return $this;
	}

	private function whereIn($column, $values, $boolean = 'AND', $not = false)
	{
		$type = ($not) ? 'notIn' : 'In';

		$values = (!is_array($values)) ? array($values) : $values;

		$this->components['where'][] = compact('type', 'column', 'values', 'boolean');

		$this->bindings['where'] = array_merge($this->bindings['where'], array_filter(array_values($values), function($value) {
			return !($value instanceof Expression);
		}));

		return $this;
	}

	/**
	 * Добавляет limit
	 */
	public function limit($offset = null, $limit = null)
	{
		if (!in_array(func_num_args(), [1,2])) {
			return $this;
		}

		$limit = array_map(function($value) {
			return max(0, $value);
		}, func_get_args());

		$this->components['limit'] = $limit;

		$this->bindings['limit'] = array_filter($limit, function($binding) {
			return !($binding instanceof Expression);
		});

		return $this;
	}

	/**
	 * Добавляет order by
	 */
	public function order($column, $direction = 'ASC')
	{
		$direction = (strtoupper($direction) == 'ASC') ? 'ASC' : 'DESC';

		$this->components['order'][] = compact('column', 'direction');

		return $this;
	}

	/**
	 * Добвляет group by
	 */
	public function group($columns)
	{
		$columns = is_array($columns) ? $columns : func_get_args();

		$this->components['group'] = array_merge($this->components['group'], $columns);

		return $this;
	}

	/**
	 * Добавляет having
	 */
	public function having($column, $operator = null, $value = null, $boolean = 'AND')
	{
		if (func_num_args() == 2) {
			list($operator, $value) = array('=', $operator);
		}

		if (count($this->components['having']) == 0) {
			$boolean = '';
		}

		$operator = strtoupper($operator);

		if (!in_array($operator, $this->operators)) {
			return $this;
		}

		$type = 'base';

		$this->components['having'][] = compact('type', 'column', 'operator', 'value', 'boolean');

		if (!($value instanceof Expression)) {
			$this->bindings['having'][] = $value;
		}

		return $this;
	}

	public function andHaving($column, $operator = null, $value = null)
	{
		$this->having($column, $operator, $value, 'AND');

		return $this;
	}

	public function orHaving($column, $operator = null, $value = null)
	{
		$this->having($column, $operator, $value, 'OR');

		return $this;
	}

	/**
	 * Добавляет delete
	 */
	public function delete()
	{
		$this->type = 'delete';

		$this->components['delete'] = array(true);
		$this->components['from'] = $this->table;

		$result = $this->exec();

		return $result->rowCount();
	}

	/**
	 * Добавляет update
	 */
	public function update($params)
	{
		if (!is_array($params)) {
			return $this;
		}

		$this->type = 'update';

		$this->components['update'] = $this->table;
		$this->components['set'] = $params;

		$this->bindings['set'] = array_merge($this->bindings['set'], array_filter(array_values($params), function($param) {
			return !($param instanceof Expression);
		}));

		$result = $this->exec();

		return $result->rowCount();
	}

	/**
	 * Добавляет insert
	 */
	public function insert($params)
	{
		if (!is_null($this->type) AND $this->type != 'insert') {
			return $this;
		}

		if (!is_array($params)) {
			return $this;
		}

		if (!is_array(reset($params))) {
			$params = array($params);
		}
		else {
			foreach ($params as $column => $value) {
				ksort($value);
				$params[$column] = $value;
			}
		}

		$this->type = 'insert';

		$this->components['insert'] = array(true);
		$this->components['into'] = $this->table;

		$this->components['columns'] = array_keys(reset($params));

		foreach ($params as $values) {
			$this->components['values'][] = array_values($values);
		}

		foreach ($params as $values) {
			$this->bindings['values'] = array_merge($this->bindings['values'], array_filter(array_values($values), function($value) {
				return !($value instanceof Expression);
			}));
		}

		$result = $this->exec();

		return $result->lastInsertId();
	}

	/**
	 * Добавляет count
	 */
	public function count($columns = array('*'))
	{
		$this->type = 'aggregate';

		$function = 'count';
		$columns = is_array($columns) ? $columns : func_get_args();

		$this->components['aggregate'] = compact('function', 'columns');
		$this->components['from'] = $this->table;

		$result = $this->exec();

		return $result->getList()[0];
	}

	/**
	 * Нужны только уникальные значения
	 */
	public function distinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Производит запрос к базе данных
	 */
	public function exec()
	{
		$data = array();
		$bindings = array();

		foreach ($this->bindings_map[$this->type] as $value) {
			$bindings[$value] = $this->bindings[$value];
		}

		array_walk_recursive($bindings, function($value) use (&$data) { $data[] = $value; });

		return $this->dbq->query($this->sql(), $data);
	}

	/**
	 * Возвращает созданую SQL строку
	 */
	public function sql()
	{
		return (new \Knee\DataBase\Builder\Compile())->compile($this);
	}
}

?>
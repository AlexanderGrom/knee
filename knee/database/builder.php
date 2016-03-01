<?php
/*
 * Конструктор запросов
 */

namespace Knee\DataBase;

use Closure;

class Builder
{
    /**
     * Объект Query
     *
     * @var \Knee\DataBase\Query
     */
    protected $dbq = null;

    /**
     * Тип запроса(select/insert/update/delete)
     *
     * @var string
     */
    public $type = null;

    /**
     * Таблица с которой работаем
     *
     * @var string
     */
    public $table = null;

    /**
     * Требуются ли только уникальные значения
     *
     * @var boolean
     */
    public $distinct = false;

    /**
     * Составляем карту значений для плейсехолдеров
     *
     * @var array
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
     *
     * @var array
     */
    public $bindings_map = array(
        'aggregate' => ['where'],
        'select'    => ['where','having','limit'],
        'insert'    => ['values'],
        'update'    => ['set','where','limit'],
        'delete'    => ['where','limit']
    );

    /**
     * Компоненты запроса
     *
     * @var array
     */
    public $components = array(
        'aggregate' => [],
        'select' => [],
        'insert' => [],
        'update' => [],
        'delete' => [],
        'from' => [],
        'join' => [],
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
     *
     * @var array
     */
     public $components_map = array(
        'aggregate' => ['aggregate','from','join','where'],
        'select'    => ['select','from','join','where','group','having','order','limit'],
        'insert'    => ['insert','into','columns','values'],
        'update'    => ['update','set','where','limit'],
        'delete'    => ['delete','from','where','limit']
    );

    /**
     * Доступные операторы
     *
     * @var array
     */
    protected $operators = array(
        '=', '!=', '<', '>', '<=', '>=', '<>',
        'IN', 'NOT IN',
        'LIKE', 'NOT LIKE',
        '&', '|', '^', '<<', '>>',
        'RLIKE', 'REGEXP', 'NOT REGEXP',
    );

    /**
     * Конструктор
     *
     * @param \Knee\DataBase\Query - объект Query
     */
    public function __construct($dbq)
    {
        $this->dbq = $dbq;
    }

    /**
     * Добавляет select
     *
     * @param array $columns - поля
     * @return \Knee\DataBase\Statement
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
     *
     * @param string $columns - имя таблицы
     * @return this
     */
    public function table($name)
    {
        $this->table = is_array($name) ? $name : func_get_args();

        return $this;
    }

    /**
     * Добавляет Inner Join
     *
     * @param  string  $table
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @param  string  $type
     * @return this
     */
    public function join($table, $column1, $operator = null, $column2 = null, $type = 'INNER')
    {
        $join = new Builder\Join($type, $table);

        if ($column1 instanceof Closure) {
            call_user_func($column1, $join);
        } else {
            $join->on($column1, $operator, $column2);
        }

        $this->components['join'][] = $join;

        return $this;
    }

    /**
     * Добавляет Left Join
     *
     * @param  string  $table
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @return this
     */
    public function leftJoin($table, $column1, $operator = null, $column2 = null)
    {
        return $this->join($table, $column1, $operator, $column2, 'LEFT');
    }

    /**
     * Добавляет where
     *
     * @param string $column - поле
     * @param string $operator - оператор
     * @param string $value - значение
     * @param string $boolean - AND или OR
     * @return this
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

    /**
     * Добавляет and where
     *
     * @param string $column - поле
     * @param string $operator - оператор
     * @param string $value - значение
     * @return this
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        $this->where($column, $operator, $value, 'AND');

        return $this;
    }

    /**
     * Добавляет or where
     *
     * @param string $column - поле
     * @param string $operator - оператор
     * @param string $value - значение
     * @return this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->where($column, $operator, $value, 'OR');

        return $this;
    }

    /**
     * Добавляет группу where
     *
     * @param Closure $callback - калбэк
     * @param string $boolean - AND или OR
     * @return this
     */
    protected function whereGroup(Closure $callback, $boolean = 'AND')
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

    /**
     * Добавляет проверку на NULL
     */
    protected function whereNull($column, $boolean = 'AND', $not = false)
    {
        $type = ($not) ? 'notNull' : 'Null';

        $this->components['where'][] = compact('type', 'column', 'boolean');

        return $this;
    }

    /**
     * Добавляет IN
     */
    protected function whereIn($column, $values, $boolean = 'AND', $not = false)
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
     *
     * @return int
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
     *
     * @param array $params
     * @return int
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
     *
     * @param array $params
     * @return int
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
     *
     * @return \Knee\DataBase\Statement
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
     *
     * @return string
     */
    public function sql()
    {
        return (new \Knee\DataBase\Builder\Compile())->compile($this);
    }
}

?>
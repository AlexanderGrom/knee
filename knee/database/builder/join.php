<?php

/*
 * Описание Join для конструктора запросов
 */

namespace Knee\DataBase\Builder;

class Join
{
    /**
     * Тип Join`a (INNER или LEFT)
     *
     * @var string
     */
    public $type;

    /**
     * Таблица для Join`a
     *
     * @var string
     */
    public $table;

    /**
     * Условия On
     *
     * @var array
     */
    public $conditions = array();

    /**
     * Конструктор
     *
     * @param  string  $type
     * @param  string  $table
     */
    public function __construct($type, $table)
    {
        $this->type = $type;
        $this->table = $table;
    }

    /**
     * Добавляет ON
     *
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @param  string  $connector
     * @return this
     */
    public function on($column1, $operator, $column2, $connector = 'AND')
    {
        $this->conditions[] = compact('column1', 'operator', 'column2', 'connector');

        return $this;
    }

    /**
     * Добавляет OR ON(...)
     *
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @return this
     */
    public function orOn($column1, $operator, $column2)
    {
        return $this->on($column1, $operator, $column2, 'OR');
    }

    /**
     * Добавляет AND ON(...)
     *
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @return this
     */
    public function andOn($column1, $operator, $column2)
    {
        return $this->on($column1, $operator, $column2, 'AND');
    }
}
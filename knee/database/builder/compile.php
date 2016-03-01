<?php
/*
 * Компиляция конструктора запросов
 */

namespace Knee\DataBase\Builder;

use Knee\DataBase\Builder;
use Knee\DataBase\Expression;

class Compile
{
    /**
     * Обертка обратными кавычками
     *
     * @param string $value
     * @return string
     */
    public function wrap($value)
    {
        if ($value instanceof Expression) {
            return $value->get();
        }

        $value = strtolower($value);

        $segments = explode(" ", $value);
        $segments = array_diff($segments, array(''));

        if (in_array('as', $segments) !== false AND count($segments) == 3) {
            return $this->wrap($segments[0]).' as '.$this->wrap($segments[2]);
        } else if (count($segments) == 2) {
            return $this->wrap($segments[0]).' as '.$this->wrap($segments[1]);
        }

        $segments = explode('.', $value);

        $wrapped = array();

        foreach ($segments as $segment) {
            $wrapped[] = ($segment != '*') ? '`'.str_replace('`', '``', $segment).'`' : $segment;
        }

        return implode('.', $wrapped);
    }

    /**
     * Обертывание массива
     *
     * @param array $values
     * @return string
     */
    public function wraps($values)
    {
        return implode(', ', array_map(array($this, 'wrap'), $values));
    }

    /**
     * Составление параметров
     *
     * @param array $params
     * @return string
     */
    public function parameters($params)
    {
        return implode(', ', array_map(function($value) {
            return ($value instanceof Expression) ? $value->get() : '?';
        }, $params));
    }

    /**
     * Компиляция Select
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileSelect(Builder $builder)
    {
        $select = ($builder->distinct) ? 'SELECT DISTINCT' : 'SELECT';

        return $select.' '.$this->wraps($builder->components['select']);
    }

    /**
     * Компиляция агрегатной функции
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileAggregate(Builder $builder)
    {
        $aggregate = $builder->components['aggregate'];

        $method = 'aggregate'.ucfirst($aggregate['function']);

        return "SELECT ".$this->$method($builder, $aggregate['columns']);
    }

    /**
     * Компиляция агрегатной функции COUNT(*)
     *
     * @param \Knee\Database\Builder $builder
     * @param array $columns
     * @return string
     */
    public function aggregateCount(Builder $builder, $columns)
    {
        $columns = $this->wraps($columns);

        $distinct = ($builder->distinct AND $columns != '*') ? 'DISTINCT ' : '';

        return "COUNT(".$distinct.$columns.")";
    }

    /**
     * Компиляция From
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileFrom(Builder $builder)
    {
        return "FROM ".$this->wraps($builder->components['from']);
    }

    /**
     * Компиляция JOIN
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileJoin(Builder $builder)
    {
        $sql = array();

        foreach ($builder->components['join'] as $join)
        {
            $table = $this->wrap($join->table);

            $conditions = array();

            $first = true;
            foreach ($join->conditions as $condition)
            {
                extract($condition);

                $column1 = $this->wrap($column1);
                $column2 = $this->wrap($column2);

                if ($first) {
                    $conditions[] = $column1.' '.$operator.' '.$column2;
                } else {
                    $conditions[] = $connector.' '.$column1.' '.$operator.' '.$column2;
                }

                $first = false;
            }

            $conditions = implode(' ', $conditions);

            $sql[] = $join->type.' JOIN '.$table.' ON '.$conditions;
        }

        return implode(' ', $sql);
    }

    /**
     * Компиляция WHERE
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileWhere(Builder $builder)
    {
        $sql = array();

        foreach ($builder->components['where'] as $where) {
            $method = 'where'.ucfirst($where['type']);

            $sql[] = trim($where['boolean'].' '.$this->$method($builder, $where));
        }

        return 'WHERE '.implode(' ', $sql);
    }

    protected function whereBase(Builder $builder, $where)
    {
        return $this->wrap($where['column']).' '.$where['operator'].' '.$this->parameters(array($where['value']));
    }

    protected function whereGroup(Builder $builder, $where)
    {
        return '('.substr($this->compileWhere($where['builder']), 6).')';
    }

    protected function whereIn(Builder $builder, $where)
    {
        return $this->wrap($where['column']).' IN ('.$this->parameters($where['values']).')';
    }

    protected function whereNotIn(Builder $builder, $where)
    {
        return $this->wrap($where['column']).' NOT IN ('.$this->parameters($where['values']).')';
    }

    protected function whereNull(Builder $builder, $where)
    {
        return $this->wrap($where['column']).' IS NULL';
    }

    protected function whereNotNull(Builder $builder, $where)
    {
        return $this->wrap($where['column']).' IS NOT NULL';
    }

    /**
     * Компиляция Group By
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileGroup(Builder $builder)
    {
        return "GROUP BY ".$this->wraps($builder->components['group']);
    }

    /**
     * Компиляция Having
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileHaving(Builder $builder)
    {
        $sql = array();

        foreach ($builder->components['having'] as $having) {
            $method = 'having'.ucfirst($having['type']);

            $sql[] = trim($having['boolean'].' '.$this->$method($builder, $having));
        }

        if (count($sql) > 0) {
            return 'HAVING '.implode(' ', $sql);
        }

        return '';
    }

    protected function havingBase(Builder $builder, $having)
    {
        return $this->wrap($having['column']).' '.$having['operator'].' '.$this->parameters(array($having['value']));
    }

    /**
     * Компиляция Group By
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileOrder(Builder $builder)
    {
        $sql = array();

        foreach ($builder->components['order'] as $order) {
            $sql[] = $this->wrap($order['column']).' '.$order['direction'];
        }

        return "ORDER BY ".implode(', ', $sql);
    }

    /**
     * Компиляция Limit
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileLimit(Builder $builder)
    {
        return "LIMIT ".$this->parameters($builder->components['limit']);
    }

    /**
     * Компиляция Update
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileUpdate(Builder $builder)
    {
        return "UPDATE ".$this->wraps($builder->components['update']);
    }

    /**
     * Компиляция Set
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileSet(Builder $builder)
    {
        $sql = array();

        foreach ($builder->components['set'] as $column => $value) {
            $sql[] = $this->wrap($column).' = '.$this->parameters(array($value));
        }

        return "SET ".implode(', ', $sql);
    }

    /**
     * Компиляция Insert
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileInsert(Builder $builder)
    {
        return "INSERT IGNORE";
    }

    /**
     * Компиляция Into
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileInto(Builder $builder)
    {
        return "INTO ".$this->wraps($builder->components['into']);
    }

    /**
     * Компиляция Columns
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileColumns(Builder $builder)
    {
        return "(".$this->wraps($builder->components['columns']).")";
    }

    /**
     * Компиляция Values
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileValues(Builder $builder)
    {
        $parameters = $this->parameters(reset($builder->components['values']));

        $values = array_fill(0, count($builder->components['values']), "(".$parameters.")");

        return 'VALUES '.implode(', ', $values);
    }

    /**
     * Компиляция Delete
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compileDelete(Builder $builder)
    {
        return "DELETE";
    }

    /**
     * Компиляция компонентов по карте
     *
     * @param \Knee\Database\Builder $builder
     * @return string
     */
    public function compile(Builder $builder)
    {
        $sql = array();
        $type = $builder->type;

        foreach ($builder->components_map[$type] as $component) {
            if (count($builder->components[$component]) > 0) {
                $method = 'compile'.ucfirst($component);
                $sql[$component] = $this->$method($builder);
            }
        }

        return implode(' ',  $sql);
    }
}

?>
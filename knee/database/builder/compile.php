<?php
/*
 * Knee framework
 * Назначение: Работа с базой данный (Компиляция конструктора запросов)
 */

namespace Knee\DataBase\Builder;

use Knee\DataBase\Builder;
use Knee\DataBase\Expression;

class Compile
{
	/**
	 * Конструктор
	 */
	public function __construct() { }

	/**
	 * Обертка обратными кавычками
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
	 */
	public function wraps($values)
	{
		return implode(', ', array_map(array($this, 'wrap'), $values));
	}

	/**
	 * Составление параметров
	 */
	public function parameters($params)
	{
		return implode(', ', array_map(function($value) {
			return ($value instanceof Expression) ? $value->get() : '?';
		}, $params));
	}

	/**
	 * Компиляция Select
	 */
	public function compileSelect(Builder $builder)
	{
		$select = ($builder->distinct) ? 'SELECT DISTINCT' : 'SELECT';

		return $select.' '.$this->wraps($builder->components['select']);
	}

	/**
	 * Компиляция агрегатной функции
	 */
	public function compileAggregate(Builder $builder)
	{
		$aggregate = $builder->components['aggregate'];

		$method = 'aggregate'.ucfirst($aggregate['function']);

		return "SELECT ".$this->$method($builder, $aggregate['columns']);
	}

	/**
	 * Компиляция агрегатной функции COUNT(*)
	 */
	public function aggregateCount(Builder $builder, $columns)
	{
		$columns = $this->wraps($columns);

		$distinct = ($builder->distinct AND $columns != '*') ? 'DISTINCT ' : '';

		return "COUNT(".$distinct.$columns.")";
	}

	/**
	 * Компиляция From
	 */
	public function compileFrom(Builder $builder)
	{
		return "FROM ".$this->wraps($builder->components['from']);
	}

	/**
	 * Компиляция WHERE
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
	 */
	public function compileGroup(Builder $builder)
	{
		return "GROUP BY ".$this->wraps($builder->components['group']);
	}

	/**
	 * Компиляция Having
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
	 */
	public function compileLimit(Builder $builder)
	{
		return "LIMIT ".$this->parameters($builder->components['limit']);
	}

	/**
	 * Компиляция Update
	 */
	public function compileUpdate(Builder $builder)
	{
		return "UPDATE ".$this->wraps($builder->components['update']);
	}

	/**
	 * Компиляция Set
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
	 */
	public function compileInsert(Builder $builder)
	{
		return "INSERT IGNORE";
	}

	/**
	 * Компиляция Into
	 */
	public function compileInto(Builder $builder)
	{
		return "INTO ".$this->wraps($builder->components['into']);
	}

	/**
	 * Компиляция Columns
	 */
	public function compileColumns(Builder $builder)
	{
		return "(".$this->wraps($builder->components['columns']).")";
	}

	/**
	 * Компиляция Values
	 */
	public function compileValues(Builder $builder)
	{
		$parameters = $this->parameters(reset($builder->components['values']));

		$values = array_fill(0, count($builder->components['values']), "(".$parameters.")");

		return 'VALUES '.implode(', ', $values);
	}

	/**
	 * Компиляция Delete
	 */
	public function compileDelete(Builder $builder)
	{
		return "DELETE";
	}

	/**
	 * Компиляция компонентов по карте
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
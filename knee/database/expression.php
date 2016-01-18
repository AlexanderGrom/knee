<?php
/*
 * Knee framework
 * Назначение: Работа с базой данный (Сырые выражения (RAW))
 */

namespace Knee\DataBase;

class Expression
{
	/**
	 * Содержит выражение
	 */
	private $expression;

	/**
	 * Создает выражение
	 */
	public function __construct($expression)
	{
		$this->expression = $expression;
	}

	/**
	 * Возвращает выражение
	 */
	public function get()
	{
		return $this->expression;
	}

	/**
	 * Возвращает выражение ввиде строки
	 */
	public function __toString()
	{
		return (string) $this->get();
	}

}

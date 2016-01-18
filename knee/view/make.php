<?php
/*
 * Knee framework
 * Назначение: Make шаблонизатора
 */

namespace Knee\View;

class Make
{
	/**
	 * Массив замен
	 */
	private $data = array();

	/**
	 * Путь к шаблону
	 */
	private $path = null;

	/**
	 * Конструктор
	 */
	public function __construct($path, $data)
	{
		$this->path = $path;
		$this->data = $data;
	}

	/**
	 * Добавление данных data
	 */
	public function add($data)
	{
		$this->data = array_merge($this->data, $data);
	}

	/**
	 * Очистка данных data
	 */
	public function clear()
	{
		$this->data = array();
	}

	/**
	 * Добавляем данные в шаблон
	 */
	public function with($name, $value)
	{
		$segments = explode('.', $name);

		$diff = array_diff($segments, array(''));
		if ((count($segments) - count($diff)) != 0) return $this;

		$data =& $this->data;
		foreach ($segments as $segment) {
			if (!array_key_exists($segment, $data) OR ! is_array($data[$segment])) {
				$data[$segment] = array();
			}

			$data =& $data[$segment];

		}

		$data = $value;

		return $this;
	}

	/**
	 * Добавляем данные в шаблон
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Получаем данные
	 */
	public function __get($name)
	{
		return (array_key_exists($name, $this->data)) ? $this->data[$name] : null;
	}

	/**
	 * Проверяем данные на доступность
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * Удаляем данные
	 */
	public function __unset($name)
	{
		unset($this->data[$name]);
	}

	/**
	 * Ручная компиляция
	 */
	public function compile()
	{
		$compile = new \Knee\View\Compile($this->path, $this->data);

		return (string) $compile->result();
	}

	/**
	 * Строковое представление объекта
	 */
	public function __toString()
	{
		return $this->compile();
	}
}

?>
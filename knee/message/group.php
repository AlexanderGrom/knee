<?php
/*
 * Knee framework
 * Назначение: Групировка сообщений
 */

namespace Knee\Message;

class Group
{
	/**
	 * Массив объектов контейнеров сообщений
	 */
	private $containers = array();

	/**
	 * Активный контейнер сообщений
	 */
	private $container = null;

	/**
	 * Конструктор
	 */
	public function __construct() {}

	/**
	 * Создание нового контейнера для сообщений
	 */
	public function start()
	{
		$this->container = new \stdClass();
		$this->container->messages = array();

		$this->containers[] = $this->container;
		return true;
	}

	/**
	 * Добавление сообщения
	 */
	public function set($value = '')
	{
		if (is_object($this->container)) {
			$this->container->messages[] = $value;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Получение сообщений в виде текста
	 */
	public function getText()
	{
		if (is_object($this->container)) {
			return implode(PHP_EOL, $this->container->messages);
		} else {
			return '';
		}
	}

	/**
	 * Получение сообщений в виде массива
	 */
	public function getList()
	{
		if (is_object($this->container)) {
			return $this->container->messages;
		} else {
			return array();
		}
	}

	/**
	 * Получение сообщений в виде данных в формате JSON
	 */
	public function getJSON()
	{
		if (is_object($this->container)) {
			return json_encode($this->container->messages);
		} else {
			return json_encode(array());
		}
	}

	/**
	 * Получение сообщений в виде данных в формате HTML
	 */
	public function getHTML()
	{
		if (is_object($this->container)) {
			$html = "<ul>";
			foreach ($this->container->messages as $value) {
				$html .= "<li>".$value."</li>";
			}
			$html .= "</ul>";

			return $html;
		} else {
			return '';
		}
	}

	/**
	 * Получение кол-ва сообщений
	 */
	public function count()
	{
		if (is_object($this->container)) {
			return count($this->container->messages);
		} else {
			return 0;
		}
	}

	/**
	 * Получение уровня контейнера сообщений
	 */
	public function level()
	{
		return count($this->containers);
	}

	/**
	 * Удаление всех сообщений в контейнере
	 */
	public function clear()
	{
		if (is_object($this->container)) {
			$this->container->messages = array();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Закрытие последнего контейнера
	 */
	public function end()
	{
		if (count($this->containers) > 0) {
			array_splice($this->containers, -1);
			$this->container = (count($this->containers) > 0) ? end($this->containers) : null;
			return true;
		} else {
			return false;
		}
	}
}

?>
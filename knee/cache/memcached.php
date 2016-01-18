<?php
/*
 * Knee framework
 * Назначение: Работа с кэшем библиотеку memcached
 */

namespace Knee\Cache;

use Config, Error, Lang, Time;

class Memcached
{
	/**
	 * Коннект к memcached
	 */
	private $connection = null;

	/**
	 * Ключ-прификс
	 */
	private $token = null;

	/**
	 * Подключение
	 */
	public function __construct()
	{
		$this->token = Config::get('cache.token');

		if (!class_exists('Memcached')) {
			Error::e503(Lang::get('system.cache.nodriver'));
		}

		return $this->connection();
	}

	/**
	 * Подключение
	 */
	private function connection()
	{
		$memcached = new \Memcached;

		$servers = Config::get('cache.servers');

		foreach ($servers as $server) {
			$memcached->addServer($server['host'], $server['port'], $server['weight']);
		}

		return $memcached;
	}

	/**
	 * Получение соединения
	 */
	public function connect()
	{
		if (!is_object($this->connection)) {
			$this->connection = $this->connection();
		}

		return $this->connection;
	}

	/**
	 * Добавление данных в кэш с перезаписью зачений
	 */
	public function set($key, $value, $tags = array(), $expire = 0)
	{
		$connect = $this->connect();

		if (!is_object($connect)) {
			return false;
		}

		if ((int)$expire != 0) {
			$expire = Time::now() + Time::relative($expire);
		} else {
			$expire = 0;
		}

		$tags_values = array();
		if (count($tags) > 0) {
			$microtime = microtime(true);

			foreach ($tags as $tag) {
				if (($tag_value = $connect->get($this->token.'tag.'.$tag)) !== false) {
					$tags_values[$tag] = $tag_value;
				} else {
					$tags_values[$tag] = $microtime;
					$connect->add($this->token.'tag.'.$tag, $microtime, 0);
				}
			}
		}

		$values = array();
		$values['value'] = $value;
		if (count($tags_values) > 0) {
			$values['tags'] = $tags_values;
		}

		return $connect->set($this->token.'var.'.$key, $values, $expire);
	}

	/**
	 * Получение значения из кэша
	 */
	public function get($key)
	{
		$connect = $this->connect();

		if (!is_object($connect)) {
			return null;
		}

		if (($values = $connect->get($this->token.'var.'.$key)) !== false) {
			if (!is_array($values)) {
				return null;
			}

			if (isset($values['tags'])) {
				foreach ($values['tags'] as $tag_key => $tag_val) {
					if ($tag_val != $connect->get($this->token.'tag.'.$tag_key)) {
						return null;
					}
				}
			}

			return (isset($values['value'])) ? $values['value'] : null;
		}
		else {
			return null;
		}
	}

	/**
	 * Удаление данных из кэша
	 */
	public function del($key)
	{
		$connect = $this->connect();

		if (!is_object($connect)) {
			return false;
		}

		return $connect->delete($this->token.'var.'.$key);
	}

	/**
	 * Проверка существования данных в кэше, учитывает обнуление тегов
	 */
	public function exists($key)
	{
		return ($this->get($key) !== null) ? true : false;
	}

	/**
	 * "Удалить" все объекты или обнулить тег
	 */
	public function clear($tags = array())
	{
		$connect = $this->connect();

		if (!is_object($connect)) {
			return false;
		}

		if (count($tags) > 0) {
			$microtime = microtime(true);

			foreach ($tags as $tag) {
				$connect->replace($this->token.'tag.'.$tag, $microtime, 0);
			}

			return true;
		} else {
			return $connect->flush();
		}
	}

	/**
	 * Добавление данных в кэш (для внутренних нужд)
	 */
	public function add($key, $value, $expire = 0)
	{
		$connect = $this->connect();

		if (!is_object($connect)) {
			return false;
		}

		if ((int)$expire != 0) {
			$expire = Time::now() + $expire;
		} else {
			$expire = 0;
		}

		$tags_values = array();
		if (count($tags) > 0) {
			$microtime = microtime(true);

			foreach ($tags as $tag) {
				if (($tag_value = $connect->get($this->token.'tag.'.$tag)) !== false) {
					$tags_values[$tag] = $tag_value;
				} else {
					$tags_values[$tag] = $microtime;
					$connect->add($this->token.'tag.'.$tag, $microtime, 0);
				}
			}
		}

		$values = array();
		$values['value'] = $value;
		if (count($tags_values) > 0) {
			$values['tags'] = $tags_values;
		}

		return $connect->add($this->token.'var.'.$key, $values, $expire);
	}
}

?>
<?php
/*
 * Knee framework
 * Назначение: Хранение объектов в кратковременной памяти
 */

namespace Knee;

class Memory
{
	/**
	 * Массив временных данных
	 */
	private static $memory = array();

	/**
	 * Получение данных из памяти
	 */
	public static function get($key)
	{
		return (array_key_exists($key, static::$memory)) ? static::$memory[$key] : null;
	}

	/**
	 * Добавление данных в память
	 */
	public static function set($key, $value)
	{
		static::$memory[$key] = $value;
		return true;
	}

	/**
	 * Удаление данных из памяти
	 */
	public static function del($key)
	{
		if (array_key_exists($key, static::$memory))  {
			unset(static::$memory[$key]);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Проверка существования данных в сессии
	 */
	public static function exists($key)
	{
		return (array_key_exists($key, static::$memory)) ? true : false;
	}

	/**
	 * Удаление всех данных
	 */
	public static function clear()
	{
		static::$memory = array();
		return true;
	}
}

?>
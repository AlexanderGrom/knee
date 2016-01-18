<?php
/*
 * Knee framework
 * Назначение: Реализация блоков
 */

namespace Knee;

class Block
{
	/**
	 * Массив с блоками
	 */
	private static $blocks = array();

	/**
	 * Массив буфера блоков
	 */
	private static $buffers = array();

	/**
	 * Получение блоков
	 */
	public static function get($path)
	{
		if (array_key_exists($path, static::$blocks)) {
			return static::$blocks[$path];
		}

		$parse_path = explode(".", $path);

		$diff = array_diff($parse_path, array(''));
		if ((count($parse_path) - count($diff)) != 0) return false;

		foreach ($parse_path as $value) {
			if (substr($value, 0, 1) == '_') return false;
		}

		$file_path = ROOT_PATH.'/app/blocks/'.implode("/", $parse_path).'.php';

		if (is_file($file_path)) {
			require_once($file_path);

			array_walk($parse_path, function(&$item){
				$item = ucwords($item);
			});

			$class = implode("_", $parse_path)."_Block";

			if (!class_exists($class)) {
				array_pop($parse_path);
				array_push($parse_path, $class);

				$class = '\\App\\Blocks\\'.implode('\\', $parse_path);
			}

			return static::$blocks[$path] = new $class();
		} else {
			return false;
		}
	}

	/**
	 * Работа с буфером блоков
	 */
	public static function buffer($name)
	{
		if (array_key_exists($name, static::$buffers)) {
			return static::$buffers[$name];
		}

		static::$buffers[$name] = new \Knee\Block\Buffer();

		return static::$buffers[$name];
	}
}

?>
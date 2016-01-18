<?php
/*
 * Knee framework
 * Назначение: Загрузчик скриптов
 */

namespace Knee;

class Loader
{
	/**
	 * Массив с псевдонимами пространств имен для автозагрузчика
	 */
	private static $aliases = array();

	/**
	 * Массив с картой class => path для автозагрузчика
	 */
	private static $mappings = array();

	/**
	 * Регистрация автозагрузчика
	 */
	public static function autoload()
	{
		static::$aliases = static::path(ROOT_PATH.'/app/aliases.php');

		spl_autoload_register(array('Knee\\Loader', '__autoload'));
	}

	/**
	 * Автозагрузчик
	 */
	public static function __autoload($class)
	{
		if (isset(static::$aliases[$class])) {
			return class_alias(static::$aliases[$class], $class);
		}

		if (isset(static::$mappings[$class])) {
			if (is_file($path = static::$mappings[$class])) {
				return require($path);
			}
		}

		$class = str_replace('\\', '/', $class);

		$class_normal = $class;
		$class_lower = mb_strtolower($class);

		if (is_file($path = ROOT_PATH.'/'.$class_lower.'.php')) {
			return require($path);
		}
		else if (is_file($path = ROOT_PATH.'/'.$class_normal.'.php')) {
			return require($path);
		}
	}

	/**
	 * Составление вспомогательной карты для автозагрузчика
	 */
	public static function map($map)
	{
		$mappings = array();
		foreach ($map as $key => $value) {
			$mappings[trim($key, '\\')]	= $value;
		}

		static::$mappings = array_merge(static::$mappings, $mappings);
	}

	/**
	 * Ручная загрузка скриптов
	 */
	public static function path($path)
	{
		do {
			$error = true;

			if ($path == "") break;
			if (!is_file($path)) break;

			$error = false;
		} while(false);

		return (!$error) ? require($path) : false;
	}
}

?>
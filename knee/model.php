<?php
/*
 * Knee framework
 * Назначение: Доступ к моделям
 */

namespace Knee;

class Model
{
	/**
	 * Массив с моделями
	 */
	private static $models = array();

	/**
	 * Получение моделей
	 */
	public static function get($path)
	{
		if (array_key_exists($path, static::$models)) {
			return static::$models[$path];
		}

		$parse_path = explode(".", $path);

		$diff = array_diff($parse_path, array(''));
		if ((count($parse_path) - count($diff)) != 0) return false;

		foreach ($parse_path as $value) {
			if (substr($value, 0, 1) == '_') return false;
		}

		$file_path = ROOT_PATH.'/app/models/'.implode("/", $parse_path).'.php';

		if (is_file($file_path)) {
			require_once($file_path);

			array_walk($parse_path, function(&$item){
				$item = ucwords($item);
			});

			$class = implode("_", $parse_path)."_Model";

			if (!class_exists($class)) {
				array_pop($parse_path);
				array_push($parse_path, $class);

				$class = '\\App\\Models\\'.implode('\\', $parse_path);
			}

			return static::$models[$path] = new $class();
		} else {
			return false;
		}
	}
}

?>
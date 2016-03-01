<?php
/*
 * Доступ к моделям
 */

namespace Knee;

class Model
{
	/**
	 * Массив с моделями
     *
     * @var array
	 */
	protected static $models = array();

	/**
	 * Получение модели
     *
     * @param string $path - точечный путь к моделе
     * @return false|object
	 */
	public static function get($path)
	{
		if (array_key_exists($path, static::$models)) {
			return static::$models[$path];
		}

		$parse_path = explode(".", $path);

		$diff = array_diff($parse_path, array(''));
		if ((count($parse_path) - count($diff)) != 0) {
            return false;
        }

		foreach ($parse_path as $value) {
			if (substr($value, 0, 1) == '_') {
                return false;
            }
		}

		$file_path = ROOT_PATH.'/app/models/'.implode("/", $parse_path).'.php';

		if (is_file($file_path)) {
			require_once($file_path);

			array_walk($parse_path, function(&$item){
				$item = ucwords($item);
			});

            $class = '\\App\\Models\\'.implode('\\', $parse_path)."_Model";

			return static::$models[$path] = new $class();
		} else {
			return false;
		}
	}
}

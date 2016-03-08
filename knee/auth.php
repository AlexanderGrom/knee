<?php
/*
 * Авторизация пользователей
 */

namespace Knee;

class Auth
{
	/**
	 * Массив с классами авторизации
     *
     * @var array
	 */
	protected static $auths = array();

	/**
	 * Получение методов авторизации
     *
     * @param string $path - путь
     * @return object
	 */
	public static function get($path)
	{
		if (array_key_exists($path, static::$auths)) {
			return static::$auths[$path];
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

		$file_path = ROOT_PATH.'/app/auths/'.implode("/", $parse_path).'.php';

		if (is_file($file_path)) {
			require_once($file_path);

			array_walk($parse_path, function(&$item){
				$item = ucwords($item);
			});

			$class = implode("_", $parse_path)."_Auth";

			if (!class_exists($class)) {
				array_pop($parse_path);
				array_push($parse_path, $class);

				$class = '\\App\\Auths\\'.implode('\\', $parse_path);
			}

			return static::$auths[$path] = new $class();
		} else {
			Error::e503(Lang::get('system.auth.noauth'));
		}
	}

	/**
	 * Магический callStatic
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::get('base'), $method), $parameters);
	}
}

?>
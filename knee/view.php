<?php
/*
 * Knee framework
 * Назначение: Шаблонизатор
 */

namespace Knee;

class View
{
	/**
	 * Массив шаблонов с которыми работаем в данный момент
	 */
	private static $makes = array();

	/**
	 * Доступ к шаблону
	 */
	public static function make($path, $data = array())
	{
		if ($path == "") return false;

		if (array_key_exists($path, static::$makes)) {
			$make = static::$makes[$path];
			$make->add($data);
		} else {
			$make = new \Knee\View\Make($path, $data);
			static::$makes[$path] = $make;
		}

		return $make;
	}
}

?>
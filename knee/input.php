<?php
/*
 * Knee framework
 * Назначение: Работа с входными данными
 */

namespace Knee;

class Input
{
	/**
	 * Массив данных $_GET + $_POST
	 */
	public static function &all()
	{
		return $_REQUEST;
	}

	/**
	 * Получение данных
	 */
	public static function get($key, $default = null)
	{
		$input = &static::all();
		$value = "";

		if (($match = static::parse_key($key)) !== false) {
			if ($match['type'] == 'array')
			{
				if (is_null($default)) $default = $match['default'];

				$keys = $match['keys'];

				$value =& $input;
				foreach ($keys as $key) {
					if ($key == "" AND is_array($value)) break;

					if (is_array($value) AND array_key_exists($key, $value)) {
						$value =& $value[$key];
					} else {
						return $default;
					}
				}

				if ($match['wait'] == 'array' AND ! is_array($value)) return $default;
				if ($match['wait'] == 'string' AND ! is_string($value)) return $default;
			}
			else if ($match['type'] == 'string')
			{
				if (is_null($default)) $default = $match['default'];

				if (array_key_exists($key, $input)) {
					$value = $input[$key];
				} else {
					return $default;
				}

				if ($match['wait'] == 'string' AND ! is_string($value)) return $default;
			}
		}

		return (is_string($value)) ? trim($value) : $value;
	}

	/**
	 * Переопределение ввода
	 */
	public static function set($key, $value)
	{
		$input = &static::all();

		if (($match = static::parse_key($key)) !== false)
		{
			if ($match['wait'] == 'array' AND ! is_array($value)) return false;
			if ($match['wait'] == 'string' AND ! is_string($value)) return false;

			if ($match['type'] == 'array') {
				$keys = $match['keys'];

				$link =& $input;
				foreach ($keys as $key)
				{
					if ($key == "") break;

					if (!array_key_exists($key, $link) OR ! is_array($link[$key])) {
						$link[$key] = array();
					}

					$link =& $link[$key];
				}

				$link = (!is_array($value)) ? (string) $value : $value;

				return true;
			}
			else if ($match['type'] == 'string') {
				$input[$key] = (string) $value;
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Проверка наличия значения
	 */
	public static function has($key)
	{
		$value = static::get($key);

		if (is_string($value) AND trim($value) == "") return false;
		if (is_array($value) AND count($value) == 0) return false;

		return true;
	}

	/**
	 * Проверка наличия ключа
	 */
	public static function exists($key)
	{
		$input = &static::all();

		$key = str_replace(" ", '', $key);

		if (($match = static::parse_key($key)) !== false)
		{
			if ($match['type'] == 'array') {
				$keys = $match['keys'];

				$link =& $input;
				foreach ($keys as $key)
				{
					if ($key == "" AND is_array($link)) break;

					if (is_array($link) AND array_key_exists($key, $link)) {
						$link =& $link[$key];
					} else {
						return false;
					}
				}

				if ($match['wait'] == 'array' AND ! is_array($link)) return false;
				if ($match['wait'] == 'string' AND ! is_string($link)) return false;

				return true;
			}
			else if ($match['type'] == 'string') {
				return (array_key_exists($key, $input) AND is_string($input[$key])) ? true : false;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Парсинг ключей
	 */
	private static function parse_key($key)
	{
		$key = str_replace(" ", '', $key);

		if (!preg_match("#^(?P<key>[a-zA-Z0-9\_\-]+)(?P<keys>(?:\[[a-zA-Z0-9\_\-]*\])+)?$#is", $key, $match)) {
			return false;
		}

		if (array_key_exists('keys', $match)) {
			$type = 'array';
			$key = $match['key'];
			$keys = '['.$match['key'].']'.$match['keys'];
			$keys = explode('][', mb_substr($keys, 1, -1));

			$keys_diff = array_diff($keys, array(''));
			$count_diff = count($keys) - count($keys_diff);

			if ($count_diff > 1) return false;
			if ($count_diff == 1 AND end($keys) != "") return false;

			if (end($keys) != "") $wait = 'string';
			else $wait = 'array';

			$default = array();
			if ($wait == 'array') $default = array();
			if ($wait == 'string') $default = '';
		} else {
			$type = 'string';
			$key = $match['key'];
			$keys = array($match['key']);
			$wait = 'string';
			$default = '';
		}

		$result = array();

		$result['type'] = $type;
		$result['key'] = $key;
		$result['keys'] = $keys;
		$result['wait'] = $wait;
		$result['default'] = $default;

		return $result;
	}

	/**
	 * Файлы
	 */
	public static function file($key = "")
	{
		$match = static::parse_key($key);

		$key = ($match !== false) ? array_shift($match['keys']) : "";
		$keys = ($match !== false) ? $match['keys'] : array();

		return new \Knee\Input\InputFile($key, $keys);
	}
}

?>
<?php
/*
 * Knee framework
 * Назначение: Подключение активных ресурсов и мета описания
 */

namespace Knee;

class Asset
{
	/**
	 * Список Link
	 */
	private static $link_list = array();

	/**
	 * Список Script
	 */
	private static $script_list = array();

	/**
	 * Список Meta
	 */
	private static $meta_list = array();

	/**
	 * Добавление нового тега link
	 */
	public static function setLink($args)
	{
		static::$link_list[] = $args;
	}

	/**
	 * Добавление нового тега script
	 */
	public static function setScript($args)
	{
		static::$script_list[] = $args;
	}

	/**
	 * Добавление нового тега meta
	 */
	public static function setMeta($args)
	{
		static::$meta_list[] = $args;
	}

	/**
	 * Получение установленных тегов link
	 */
	public static function getLink($args=array())
	{
		$html_list = array();
		foreach (static::$link_list as $items) {
			if (static::array_match_all($args, $items)) {
				$item_list = array();
				foreach ($items as $item_name => $item_value) {
					if ($item_name == 'href') {
						$item_list[] = $item_name .'="'. static::version($item_value) .'"';
					} else {
						$item_list[] = $item_name .'="'. $item_value .'"';
					}
				}

				$html_list[] = "<link ".implode(" ", $item_list).">";
			}
		}

		if (count($html_list) > 0) $html_list[] = "";

		return implode("\n", $html_list);
	}

	/**
	 * Получение установленных тегов script
	 */
	public static function getScript($args=array())
	{
		$html_list = array();
		foreach (static::$script_list as $items) {
			if (static::array_match_all($args, $items)) {
				$item_list = array();
				foreach ($items as $item_name => $item_value) {
					if ($item_name == 'src') {
						$item_list[] = $item_name .'="'. static::version($item_value) .'"';
					} else {
						$item_list[] = $item_name .'="'. $item_value .'"';
					}
				}

				$html_list[] = "<script ".implode(" ", $item_list)."></script>";
			}
		}

		if (count($html_list) > 0) $html_list[] = "";

		return implode("\n", $html_list);
	}

	/**
	 * Получение установленных тегов meta
	 */
	public static function getMeta($args=array())
	{
		$html_list = array();
		foreach (static::$meta_list as $items) {
			if (static::array_match_all($args, $items)) {
				$item_list = array();
				foreach ($items as $item_name => $item_value) {
					$item_list[] = $item_name .'="'. $item_value .'"';
				}

				$html_list[] = "<meta ".implode(" ", $item_list).">";
			}
		}

		if (count($html_list) > 0) $html_list[] = "";

		return implode("\n", $html_list);
	}

	/**
	 * Удаление установленных тегов link
	 */
	public static function delLink($args=array())
	{
		if (count($args) == 0) {
			static::$link_list = array();
			return;
		}

		foreach (static::$link_list as $key => $value) {
			if (static::array_match_all($args, $value)) unset(static::$link_list[$key]);
		}
	}

	/**
	 * Удаление установленных тегов script
	 */
	public static function delScript($args=array())
	{
		if (count($args) == 0) {
			static::$script_list = array();
			return;
		}

		foreach (static::$script_list as $key => $value) {
			if (static::array_match_all($args, $value)) unset(static::$script_list[$key]);
		}
	}

	/**
	 * Удаление установленных тегов meta
	 */
	public static function delMeta($args=array())
	{
		if (count($args) == 0) {
			static::$meta_list = array();
			return;
		}

		foreach (static::$meta_list as $key => $value) {
			if (static::array_match_all($args, $value)) unset(static::$meta_list[$key]);
		}
	}

	/**
	 * Поиск в строке по "звездочке"
	 */
	private static function match($pattern, $string)
	{
		return preg_match("#^".strtr(preg_quote($pattern, '#'),array('\*' => '.*'))."$#i", $string);
	}

	/**
	 * Поиск и проверка всех элементов массива в другом массиве с использованием поиска значений по "звездочке"
	 */
	private static function array_match_all($array_one, $array_two)
	{
		foreach ($array_one as $key => $value) {
			if (!array_key_exists($key, $array_two) OR static::match($value, $array_two[$key]) == 0) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Добавление версии скрипта к path
	 */
	private static function version($path)
	{
		$versions =& Config::get('asset');

		if (isset($versions[$path])) {
			$path .= ( ! is_null(parse_url($path, PHP_URL_QUERY))) ? '&v'.$versions[$path] : '?v'.$versions[$path];
		}

		return $path;
	}
}

?>
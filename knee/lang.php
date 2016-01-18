<?php
/*
 * Knee framework
 * Назначение: Управление языковыми файлами
 */

namespace Knee;

class Lang
{
	/**
	 * Выбранный язык
	 */
	private static $locale = "";

	/**
	 * Массив с языковыми текстами
	 */
	private static $items = array();

	/**
	 * Массив c распарсеными "точечными" путями
	 */
	private static $parsed = array();

	/**
	 * Установка языка
	 */
	public static function setLocale($lang)
	{
		if (!static::locale_exists($lang)) {
			return false;
		}

		static::$locale = $lang;

		return true;
	}

	/**
	 * Получить установленный язык
	 */
	public static function getLocale()
	{
		return static::$locale;
	}

	/**
	 * Проверить существует ли языковая директория
	 */
	public static function locale_exists($lang)
	{
		$lang = (string) $lang;

		if (preg_match('#^[a-zA-Z0-9]+$#is', $lang) == 0) return false;
		if (!is_dir(ROOT_PATH.'/app/languages/'.$lang.'/')) return false;

		return true;
	}

	/**
	 * Загрузка языковых файлов из /app/languages/
	 */
	private static function load($file_name)
	{
		if ($file_name == "") return false;

		if (array_key_exists($file_name, static::$items)) {
			return true;
		}

		$lang_name = static::$locale;
		if (!static::locale_exists($lang_name)) return false;

		$file_path = ROOT_PATH.'/app/languages/'.$lang_name.'/'.$file_name.'.php';

		if (is_file($file_path)) {
			static::$items[$file_name] = require($file_path);

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Получение значения
	 */
	public static function get($key)
	{
		if ($key == "") return null;

		$segments = static::parse($key);

		if (count($segments) != 0) static::load($segments[0]);

		$lang =& static::$items;
		foreach ($segments as $segment) {
			if (is_array($lang) AND array_key_exists($segment, $lang)) {
				$lang =& $lang[$segment];
			} else {
				return null;
			}
		}

		return $lang;
	}

	/**
	 * Переопределение значения
	 */
	public static function set($key, $value)
	{
		if ($key == "") return false;

		$segments = static::parse($key);

		if (count($segments) != 0) static::load($segments[0]);

		$lang =& static::$items;
		foreach ($segments as $segment) {
			if (is_array($lang) AND array_key_exists($segment, $lang)) {
				$lang =& $lang[$segment];
			} else {
				return false;
			}
		}

		$lang = $value;

		return true;
	}

	/**
	 * Парсинг "точечного" пути к языковому файлу
	 */
	private static function parse($path)
	{
		if ($path == "") return array();

		if (array_key_exists($path, static::$parsed)) {
			return static::$parsed[$path];
		}

		$segments = explode('.', $path);

		return static::$parsed[$path] = $segments;
	}
}

?>
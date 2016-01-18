<?php
/*
 * Knee framework
 * Назначение: Перенаправления на другой URL
 */

namespace Knee;

class Redirect
{
	/**
	 * Редирект: Перемещенно окончательно
	 */
	public static function r301($url)
	{
		$url = static::correct_url($url);

		header('Location: '.$url, true, 301);
		exit();
	}

	/**
	 * Редирект: Найдено
	 */
	public static function r302($url)
	{
		$url = static::correct_url($url);

		header('Location: '.$url, true, 302);
		exit();
	}

	/**
	 * Подготовка URL
	 */
	private static function correct_url($url)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			$url = Request::site()."/".ltrim($url, "/");
		}

		$url = preg_replace('#\#(.*?)$#is', "", $url);

		return $url;
	}
}

?>
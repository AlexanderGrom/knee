<?php
/*
 * Knee framework
 * Назначение: Установка заголовков
 */

namespace Knee;

class Header
{
	/**
	 * Установка заголовка 403 - Доступ запрещен
	 */
	public static function h403()
	{
		$header_403 = Request::protocol()." 403 Forbidden";
		@header($header_403);
	}

	/**
	 * Установка заголовка 404 - Страница не найдена
	 */
	public static function h404()
	{
		$header_404 = Request::protocol()." 404 Not Found";
		@header($header_404);
	}

	/**
	 * Установка заголовка 410 - Страница удалена
	 */
	public static function h410()
	{
		$header_410 = Request::protocol()." 410 Gone";
		@header($header_410);
	}

	/**
	 * Установка заголовка 503 - Сервис недоступен
	 */
	public static function h503()
	{
		$header_503 = Request::protocol()." 503 Service Temporarily Unavailable";
		@header($header_503);
		@header('Retry-After: 3600');
	}

	/**
	 * Установка любого другого заголовка
	 */
	public static function send($header)
	{
		@header($header);
	}
}

?>
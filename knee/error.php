<?php
/*
 * Knee framework
 * Назначение: Установка заголовоков
 */

namespace Knee;

class Error
{
	/**
	 * Выброс ошибки 403 - Доступ запрещен
	 */
	public static function e403($msg = null)
	{
		Header::h403();

		ob_end_clean_all();

		echo View::make('error.e403')
			 ->with('message', $msg)
			 ->compile();

		exit();
	}

	/**
	 * Выброс ошибки 404 - Страница не найдена
	 */
	public static function e404($msg = null)
	{
		Header::h404();

		ob_end_clean_all();

		echo View::make('error.e404')
			 ->with('message', $msg)
			 ->compile();

		exit();
	}

	/**
	 * Выброс ошибки 410 - Страница удалена
	 */
	public static function e410($msg = null)
	{
		Header::h410();

		ob_end_clean_all();

		echo View::make('error.e410')
			 ->with('message', $msg)
			 ->compile();

		exit();
	}

	/**
	 * Выброс ошибки 503 - Сервис недоступен
	 */
	public static function e503($msg = null)
	{
		Header::h503();

		ob_end_clean_all();

		echo View::make('error.e503')
			 ->with('message', $msg)
			 ->compile();

		exit();
	}
}

?>
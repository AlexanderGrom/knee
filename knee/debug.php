<?php
/*
 * Knee framework
 * Назначение: Примитивный отладчик
 */

namespace Knee;

class Debug
{
	/**
	 * Регистрация функций управления незапланированым завершением работы скрипта
	 */
	public static function register()
	{
		static::registerErrorHandler();
		static::registerExceptionHandler();
		static::registerShutdownHandler();
	}

	/**
	 * Регистрация функции отлавливающей обычные ошибки
	 */
	public static function registerErrorHandler()
	{
		set_error_handler(array('\Knee\Debug', 'handleError'));
	}

	/**
	 * Регистрация функции отлавливающей необработанные исключения
	 */
	public static function registerExceptionHandler()
	{
		set_exception_handler(array('\Knee\Debug', 'handleException'));
	}

	/**
	 * Регистрация функции отлавливающей FatalError
	 */
	public static function registerShutdownHandler()
	{
		register_shutdown_function(array('\Knee\Debug', 'handleShutdown'));
	}

	/**
	 * Обработчик обычных ошибок
	 */
	public static function handleError($level, $message, $file = '', $line = 0, $context = array())
	{
		if (error_reporting() & $level) {
			throw new \ErrorException($message, $level, 0, $file, $line);
		}
	}

	/**
	 * Обработчик непойманых исключений
	 */
	public static function handleException($exception)
	{
		ob_end_clean_all();

		$message = array();
		$message[] = '<b>File</b>: '.str_replace(ROOT_PATH, '', $exception->getFile());
		$message[] = '<b>Line</b>: '.$exception->getLine();
		$message[] = '<b>Msg</b>: '.$exception->getMessage();

		Error::e503(implode('<br>', $message));
	}

	/**
	 * Обработчик FatalError
	 */
	public static function handleShutdown()
	{
		$error = error_get_last();
		if (is_array($error) && in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE)) && strpos($error['file'], 'eval()') === false) {
			static::handleException(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
		}
	}
}

?>
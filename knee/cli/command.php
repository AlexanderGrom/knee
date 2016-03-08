<?php
/*
 * Knee framework
 * Назначение: Работа с командной строкой
 */

namespace Knee\CLI;

class Command
{
	public static function run($argv = array())
	{
		if (!static::is_cli()) {
			throw new \Exception("Works only from command line.");
		}

		list($call, $params) = static::arguments($argv);

		if (is_null($call)) {
			throw new \Exception("Path not specified.");
		}

		$parse_call = static::parse($call);

		if (is_array($parse_call)) {
			list($path, $class, $method) = $parse_call;
		} else {
			throw new \Exception("Incorrect path.");
		}

		if (is_file($path)) {
			require($path);
		} else {
			throw new \Exception("Class not found.");
		}

		if (!class_exists($class, false)) {
			throw new \Exception("Class not found.");
		}

		if (is_null($method)) {
			$method = '__construct';
		}

		$reclass = new \ReflectionClass($class);

		if (!$reclass->hasMethod($method)) {
			throw new \Exception("Method ".$method." not found in class.");
		}

		$remethod = new \ReflectionMethod($class, $method);

		if (!$remethod->isPublic()) {
			throw new \Exception("Method not public.");
		}

		$reparams = $remethod->getParameters();

		for ($i=0; $i<count($reparams); $i++) {
			if (!$reparams[$i]->isOptional() AND !isset($params[$i])) {
				throw new \Exception("Missing parameter.");
			}
		}

		if ($method == '__construct') {
			$instance = $reclass->newInstanceArgs($params);
		} else {
			$remethod->invokeArgs((new $class), $params);
		}

	}

	/**
	 * Возвращает массив с путем к классу/методу и массивом параметров
	 */
	protected static function arguments($argv)
	{
		$argv = array_splice($argv, 1);

		if (count($argv) > 0) {
			$call = $argv[0];
			$params = array_splice($argv, 1);
		} else {
			$call = null;
			$params = array();
		}

		return array($call, $params);
	}

	/**
	 * Парсит путь к вызываемому классу и методу
	 */
	protected static function parse($call)
	{
		if (mb_strpos($call, ':') !== false) {
			$parse_call = explode(':', $call);
			if (count($parse_call) > 2) return false;

			$diff = array_diff($parse_call, array(''));
			if ((count($parse_call) - count($diff)) != 0) return false;

			list($path, $method) = $parse_call;
		} else {
			list($path, $method) = array($call, null);
		}

		$parse_path = explode(".", $path);

		$diff = array_diff($parse_path, array(''));
		if ((count($parse_path) - count($diff)) != 0) return false;

		foreach ($parse_path as $value) {
			if (mb_substr($value, 0, 1) == '_') return false;
		}

		$class = mb_ucwords(implode("_", $parse_path))."_Cli";

		$path = ROOT_PATH.'/app/clis/'.implode("/", $parse_path).'.php';

		return array($path, $class, $method);
	}

	/**
	 * Проверяет запущен ли скрипт из консоли
	 */
	public static function is_cli()
	{
		return defined('STDIN') || (substr(PHP_SAPI, 0, 3) == 'cgi' && getenv('TERM'));
	}
}

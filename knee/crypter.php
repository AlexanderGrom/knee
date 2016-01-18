<?php
/*
 * Knee framework
 * Назначение: Шифрование данных по ключу
 */

namespace Knee;

class Crypter
{
	/**
	 *  Алгоритм шифрования
	 */
	public static $cipher = MCRYPT_RIJNDAEL_256;

	/**
	 * Режим шифрования
	 */
	public static $mode = MCRYPT_MODE_CBC;

	/**
	 * Размер блока шифра
	 */
	public static $block = 32;

	/**
	 * Шифрование строки
	 */
	public static function encrypt($value)
	{
		$iv = mcrypt_create_iv(static::get_iv_size(), static::randomizer());

		$value = static::pad($value);

		$key = Config::get('main.ckey');

		$value = mcrypt_encrypt(static::$cipher, $key, $value, static::$mode, $iv);

		return base64_encode($iv.$value);
	}

	/**
	 * Расшифровывание строки
	 */
	public static function decrypt($value)
	{
		$value = base64_decode($value);

		$iv = substr($value, 0, static::get_iv_size());
		$value = substr($value, static::get_iv_size());

		$key = Config::get('main.ckey');

		$value = @mcrypt_decrypt(static::$cipher, $key, $value, static::$mode, $iv);

		return static::unpad($value);
	}

	/**
	 * Получение генератора "самых" случайных чисел для системы
	 */
	public static function randomizer()
	{
		if (defined('MCRYPT_DEV_URANDOM')) {
			return MCRYPT_DEV_URANDOM;
		} elseif (defined('MCRYPT_DEV_RANDOM')) {
			return MCRYPT_DEV_RANDOM;
		} else {
			return MCRYPT_RAND;
		}
	}

	/**
	 * Получаем размер входного вектора для алгоритка шифрования и режима
	 */
	private static function get_iv_size()
	{
		return mcrypt_get_iv_size(static::$cipher, static::$mode);
	}

	/**
	 * Приводим к формату PKCS7
	 */
	private static function pad($value)
	{
		$pad = static::$block - (strlen($value) % static::$block);

		return $value.str_repeat(chr($pad), $pad);
	}

	/**
	 * Получаем исходное значение из строки в формате PKCS7
	 */
	private static function unpad($value)
	{
		$pad = ord(substr($value, -1, 1));

		$length = strlen($value);
		$before = $length - $pad;

		if (substr($value, $before) == str_repeat(substr($value, -1), $pad)) {
			return substr($value, 0, $length - $pad);
		} else {
			return $value;
		}
	}
}

?>
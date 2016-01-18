<?php
/*
 * Knee framework
 * Назначение: Работа с загруженными файлами
 */

namespace Knee\Input;

class InputFile
{
	/**
	 * Имя поля
	 */
	private $key = "";

	/**
	 * Дополнительные ключи (name='file[logos][]')
	 */
	private $keys = array();

	/**
	 * Конструктор
	 */
	public function __construct($key = "", $keys = array())
	{
		$this->key = $key;
		$this->keys = $keys;
	}

	/**
	 * Имя файла
	 */
	public function name()
	{
		$fileData = array_merge(array($this->key, 'name'), $this->keys);

		return static::get($fileData);
	}

	/**
	 * Путь к загруженному файлу
	 */
	public function path()
	{
		$fileData = array_merge(array($this->key, 'tmp_name'), $this->keys);

		return static::get($fileData);
	}

	/**
	 * Размер файла в байтах
	 */
	public function size()
	{
		$fileData = array_merge(array($this->key, 'size'), $this->keys);

		return static::get($fileData);
	}

	/**
	 * Расширение файла
	 */
	public function extension()
	{
		$fileData = array_merge(array($this->key, 'name'), $this->keys);

		return pathinfo(static::get($fileData), PATHINFO_EXTENSION);
	}

	/**
	 * Mime-type файла
	 */
	public function mime()
	{
		if (!$this->exists()) return null;

		$fileData = array_merge(array($this->key, 'tmp_name'), $this->keys);

		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), static::get($fileData));
	}

	/**
	 * Проверка наличия файла
	 */
	public function exists()
	{
		$fileData = array_merge(array($this->key, 'tmp_name'), $this->keys);

		return (mb_strlen(static::get($fileData)) != 0) ? true : false;
	}

	/**
	 * Получение значений
	 */
	private function get($keys)
	{
		$value =& $_FILES;
		foreach ($keys as $key) {
			if ($key == "" AND is_array($value)) break;

			if (is_array($value) AND array_key_exists($key, $value)) {
				$value =& $value[$key];
			} else {
				return null;
			}
		}

		return $value;
	}
}

?>
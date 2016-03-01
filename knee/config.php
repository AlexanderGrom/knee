<?php
/*
 * Работа с конфигами
 */

namespace Knee;

class Config
{
    /**
     * Массив с конфигами
     *
     * @var array
     */
    protected static $items = array();

    /**
     * Массив c распарсеными "точечными" путями к конфигам
     *
     * @var array
     */
    protected static $parsed = array();

    /**
     * Загрузка конфигуровочных файлов из /app/configs/
     *
     * @param string $file_name - точечный путь к конфигу
     */
    protected static function load($file_name)
    {
        if ($file_name == "") {
            return false;
        }

        if (array_key_exists($file_name, static::$items)) {
            return true;
        }

        $file_path = ROOT_PATH.'/app/configs/'.$file_name.'.php';

        if (is_file($file_path)) {
            static::$items[$file_name] = require($file_path);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение значения конфигурации
     *
     * @param string $key - ключ
     * @return mixed
     */
    public static function get($key)
    {
        if ($key == "") {
            return null;
        }

        $segments = static::parse($key);

        if (count($segments) != 0) {
            static::load($segments[0]);
        }

        $config = &static::$items;
        foreach ($segments as $segment) {
            if (is_array($config) AND array_key_exists($segment, $config)) {
                $config = &$config[$segment];
            } else {
                return null;
            }
        }

        return $config;
    }

    /**
     * Переопределение значения имеющейся конфигурации
     *
     * @param string $key - ключ
     * @param mixed $value - значение
     * @return boolean
     */
    public static function set($key, $value)
    {
        if ($key == "") {
            return false;
        }

        $segments = static::parse($key);

        if (count($segments) != 0) {
            static::load($segments[0]);
        }

        $config = &static::$items;
        foreach ($segments as $segment) {
            if (is_array($config) AND array_key_exists($segment, $config)) {
                $config =& $config[$segment];
            } else {
                return false;
            }
        }

        $config = $value;

        return true;
    }

    /**
     * Парсинг "точечного" пути к конфигу
     *
     * @param mixed $path - путь
     * @return array
     */
    protected static function parse($path)
    {
        if ($path == "") {
            return array();
        }

        if (array_key_exists($path, static::$parsed)) {
            return static::$parsed[$path];
        }

        $segments = explode('.', $path);

        return static::$parsed[$path] = $segments;
    }
}

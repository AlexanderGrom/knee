<?php
/*
 * Хранение объектов в кратковременной памяти
 */

namespace Knee;

class Memory
{
    /**
     * Массив временных данных
     *
     * @var array
     */
    protected static $memory = array();

    /**
     * Получение данных из памяти
     *
     * @param string $key
     * @return string|null
     */
    public static function get($key)
    {
        return (array_key_exists($key, static::$memory)) ? static::$memory[$key] : null;
    }

    /**
     * Добавление данных в память
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public static function set($key, $value)
    {
        static::$memory[$key] = $value;
        return true;
    }

    /**
     * Удаление данных из памяти
     *
     * @param string $key
     * @return boolean
     */
    public static function del($key)
    {
        if (array_key_exists($key, static::$memory))  {
            unset(static::$memory[$key]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка существования данных в сессии
     *
     * @param string $key
     * @return boolean
     */
    public static function exists($key)
    {
        return (array_key_exists($key, static::$memory)) ? true : false;
    }

    /**
     * Удаление всех данных
     *
     * @return boolean
     */
    public static function clear()
    {
        static::$memory = array();
        return true;
    }
}

?>
<?php
/*
 * Работа с сессией
 */

namespace Knee;

class Session
{
    /**
     * Коннект
     */
    private static $connection = null;

    /**
     * Старт новой сессии
     */
    public static function start()
    {
        $connect = static::connect();

        if (is_object($connect)) return true;
        else {
            static::$connection = static::connection();
        }

        return true;
    }

    /**
     * Подключение драйвера сессии
     */
    private static function connection()
    {
        $driver = mb_strtolower(Config::get('session.driver'));

        switch ($driver) {
            case 'database':
                return new Session\DataBase();
            case 'file':
                return new Session\File();
            case 'cache':
                return new Session\Cache();
            default:
                Error::e503(Lang::get('system.session.nodriver'));
        }
    }

    /**
     * Получение соединения
     */
    private static function connect()
    {
        $connect = static::$connection;

        if (is_object($connect)) return $connect;
        else return false;
    }

    /**
     * Добавление данных сессии
     */
    public static function set($key, $value)
    {
        $connect = static::connect();

        if (is_object($connect)) return $connect->set($key, $value);
        else return false;
    }

    /**
     * Получение значения сессии по ключу
     */
    public static function get($key)
    {
        $connect = static::connect();

        if (is_object($connect)) return $connect->get($key);
        else return null;
    }

    /**
     * Удаление данных сессии
     */
    public static function del($key)
    {
        $connect = static::connect();

        if (is_object($connect)) return $connect->del($key);
        else return false;
    }

    /**
     * Проверка существования данных в сессии
     */
    public static function exists($key)
    {
        $connect = static::connect();

        if (is_object($connect)) return $connect->exists($key);
        else return false;
    }

    /**
     * Создание "секретного" ключа. Например для csrf
     */
    public static function token()
    {
        $connect = static::connect();

        if (is_object($connect))  {
            $key = 'knee__session_token';

            if (static::exists($key)) {
                return static::get($key);
            } else {
                static::set($key, Str::hash(32));
                return static::get($key);
            }

        } else {
            return null;
        }
    }

    /**
     * Удаляет соединение
     */
    private static function close()
    {
        static::$connection = null;
    }

    /**
     * Дестрой сессии
     */
    public static function destroy()
    {
        $connect = static::connect();

        if (is_object($connect)) return $connect->destroy();
        else return false;
    }

    /**
     * Завершение последней начатой сессии
     */
    public static function end()
    {
        $connect = static::connect();

        if (is_object($connect)) {
            static::close();
            return $connect->save();
        } else {
            return false;
        }
    }
}

?>
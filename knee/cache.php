<?php
/*
 * Работа с кэшем
 */

namespace Knee;

class Cache
{
    /**
     * Коннект
     */
    private static $connection = null;

    /**
     * Подключение
     */
    private static function connection()
    {
        $driver = mb_strtolower(Config::get('cache.driver'));

        switch($driver) {
            case 'memcache':
                return new Cache\Memcache();
            case 'memcached':
                return new Cache\Memcached();
            default:
                Error::e503(Lang::get('system.cache.nodriver'));
        }
    }

    /**
     * Получение соединения
     */
    private static function connect()
    {
        if (!is_object(static::$connection)) {
            if ((static::$connection = static::connection()) === null) {
                Error::e503(Lang::get('system.cache.nodriver'));
            }
        }

        return static::$connection;
    }

    /**
     * Магический callStatic
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::connect(), $method), $parameters);
    }
}

?>
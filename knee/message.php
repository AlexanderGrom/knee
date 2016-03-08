<?php
/*
 * Ведение сообщений
 */

namespace Knee;

class Message
{
    /**
     * Групировки сообщений
     *
     * @var array
     */
    protected static $groups = array();

    /**
     * Работа с конкретной группой сообщений
     * 
     * @param string|null $group
     * @return object
     */
    public static function group($group = null)
    {
        $group = (!is_null($group)) ? $group : 'knee__messages';

        if (array_key_exists($group, static::$groups)) {
            return static::$groups[$group];
        }

        $groupMessages = new \Knee\Message\Group();
        static::$groups[$group] = $groupMessages;

        return static::$groups[$group];
    }

    /**
     * Магический callStatic
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::group('knee__messages'), $method), $parameters);
    }
}

?>
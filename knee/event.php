<?php
/*
 * Knee framework
 * Назначение: Работа с событиями
 */

namespace Knee;
use Closure;

class Event
{
    /**
     * Массив с событиями на прослушке
     *
     * @var array
     */
    protected static $events = array();

    /**
     * Массив с моделями событий
     *
     * @var array
     */
    protected static $models = array();

    /**
     * Установка слушателя на событие
     *
     * @param string $event
     * @param Closure $action
     * @return boolean
     */
    public static function listen($event, Closure $action)
    {
        static::$events[$event][] = $action;
        return true;
    }

    /**
     * Запуск события
     */
    public static function fire()
    {
        $params = func_get_args();
        if (count($params) == 0) {
            return false;
        }

        $event = array_shift($params);

        if (static::exists($event)) {
            foreach (static::$events[$event] as $action) {
                call_user_func_array($action, $params);
            }
        }
    }

    /**
     * Проверка существования события
     *
     * @param string $event
     * @return boolean
     */
    public static function exists($event)
    {
        return (isset(static::$events[$event])) ? true : false;
    }

    /**
     * Удаление всех данных
     *
     * @param string $event
     * @return boolean
     */
    public static function clear($event)
    {
        if (static::exists($event)) {
            unset(static::$events[$event]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение модели события
     *
     * @param string $path
     * @return object
     */
    public static function get($path)
    {
        if (array_key_exists($path, static::$models)) {
            return static::$models[$path];
        }

        $parse_path = explode(".", $path);

        $diff = array_diff($parse_path, array(''));
        if ((count($parse_path) - count($diff)) != 0) return false;

        foreach ($parse_path as $value) {
            if (mb_substr($value, 0, 1) == '_') return false;
        }

        $file_path = ROOT_PATH.'/app/events/'.implode("/", $parse_path).'.php';

        if (is_file($file_path)) {
            require_once($file_path);

            $class = mb_ucwords(implode("_", $parse_path))."_Event";

            return static::$models[$path] = new $class();
        } else {
            return false;
        }
    }
}

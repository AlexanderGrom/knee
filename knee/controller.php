<?php
/*
 * Доступ к контроллерам
 */

namespace Knee;

class Controller
{
    /**
     * Массив с контроллерами
     *
     * @var array
     */
    protected static $controllers = array();

    /**
     * Получение контроллера
     *
     * @param string $path - точечный путь к контроллеру
     * @return false|object
     */
    public static function get($path)
    {
        if (array_key_exists($path, static::$controllers)) {
            return static::$controllers[$path];
        }

        $parse_path = explode(".", $path);

        $diff = array_diff($parse_path, array(''));
        if ((count($parse_path) - count($diff)) != 0) {
            return false;
        }

        foreach ($parse_path as $value) {
            if (substr($value, 0, 1) == '_') return false;
        }

        $file_path = ROOT_PATH.'/app/controllers/'.implode("/", $parse_path).'.php';

        if (is_file($file_path)) {
            require_once($file_path);

            array_walk($parse_path, function(&$item){
                $item = ucwords($item);
            });

            $class = '\\App\\Controllers\\'.implode('\\', $parse_path)."_Controller";

            return static::$controllers[$path] = new $class();
        } else {
            return false;
        }
    }
}

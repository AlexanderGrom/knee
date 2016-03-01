<?php
/*
 * Представление, он же шаблонизатор
 *
 * Сам интерфейс работы с шаблонизатором
 * и компилятор находится в директории /knee/view/
 * Подробности работы описаны в документации.
 */

namespace Knee;

class View
{
    /**
     * Массив шаблонов с которыми работаем в данный момент
     *
     * @var array
     */
    protected static $makes = array();

    /**
     * Обращение к шаблону
     *
     * @param string $path - путь к шаблону
     * @param array $data - данные для шаблона
     *
     * @return string
     */
    public static function make($path, $data = array())
    {
        if ($path == "") {
            return false;
        }

        if (array_key_exists($path, static::$makes)) {
            $make = static::$makes[$path];
            $make->add($data);
        } else {
            $make = new \Knee\View\Make($path, $data);
            static::$makes[$path] = $make;
        }

        return $make;
    }
}

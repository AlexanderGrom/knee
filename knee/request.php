<?php
/*
 * Информация о запросе
 */

namespace Knee;

class Request
{
    /**
     * Обращение к массиву $_SERVER
     *
     * @param string $key - ключ для $_SERVER
     * @return string|array
     */
    public static function server($key = null)
    {
        return (!is_null($key)) ? $_SERVER[$key] : $_SERVER;
    }

    /**
     * Возвращает метод запроса 'GET', 'HEAD', 'POST', 'PUT'
     *
     * @return string
     */
    public static function method()
    {
        return (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : "";
    }

    /**
     * Возвращает протокол запроса
     *
     * @return string
     */
    public static function protocol()
    {
        return (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : "";
    }

    /**
     * Возвращает user agent
     *
     * @return string
     */
    public static function agent()
    {
        return (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "";
    }

    /**
     * Возвращает адрес страницы, которая привела пользователя на текущую страницу
     *
     * @return string
     */
    public static function referer()
    {
        return (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "";
    }

    /**
     * Возвращает ip пользователя
     *
     * @return string
     */
    public static function ip()
    {
        $default = '0.0.0.0';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return $default;
    }

    /**
     * Полный адрес страницы
     *
     * @return string
     */
    public static function url()
    {
        return static::scheme().'://'.static::host().static::uri();
    }

    /**
     * Полная строка запроса
     *
     * @return string
     */
    public static function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Часть адреса страницы содержащая путь
     *
     * @return string
     */
    public static function path()
    {
        return str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER["REQUEST_URI"]);
    }

    /**
     * Последний компонет адреса
     *
     * @return string
     */
    public static function base()
    {
        $path_array = explode('/', rtrim(static::path(), '/'));
        return array_pop($path_array);
    }

    /**
     * Часть адреса страницы содержащая путь без последнего компонента
     *
     * @return string
     */
    public static function dir()
    {
        $path_array = explode('/', rtrim(static::path(), '/'));
        array_pop($path_array);
        return implode('/', $path_array);
    }

    /**
     * Часть адреса страницы содержащая запрос
     *
     * @return string
     */
    public static function query()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * Возвращает имя хоста без указания порта
     *
     * @return string
     */
    public static function host()
    {
        return preg_replace('#:\d+$#', '', trim($_SERVER['HTTP_HOST']));
    }

    /**
     * Возвращает адрес сайта
     *
     * @return string
     */
    public static function site()
    {
        return static::scheme().'://'.static::host();
    }

    /**
     * Проверяет текущий путь с шаблоном по "звездочке"
     *
     * @param string $path - путь с маской
     * @return boolean
     */
    public static function path_match($path)
    {
        return Str::match($path, static::path());
    }

    /**
     * Проверяет текущий хост с шаблоном по "звездочке"
     *
     * @param string $host - хост с маской
     * @return boolean
     */
    public static function host_match($host)
    {
        return Str::match($host, static::host());
    }

    /**
     * Проверяет адрес страницы которая привела пользователя на текущую страницу с шаблоном по "звездочке"
     *
     * @param string $pattern - путь с маской
     * @return boolean
     */
    public static function referer_match($pattern)
    {
        return Str::match($pattern, static::referer());
    }

    /**
     * Возвращает схему
     *
     * @return string
     */
    public static function scheme()
    {
        return (static::is_https()) ? 'https' : 'http';
    }

    /**
     * Делает попытку определить произведен ли запрос через HTTPS протокол
     *
     * @return boolean
     */
    public static function is_https()
    {
        if (isset($_SERVER['HTTPS'])) {
            $https = mb_strtolower($_SERVER['HTTPS']);
            if ($https == 'on' || $https == '1') {
                return true;
            }
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $https = mb_strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
            if ($https == 'https') {
                return true;
            }
        }

        if (mb_stripos($_SERVER['SERVER_PROTOCOL'], 'https')) {
            return true;
        }

        return false;
    }

    /**
     * Делает попытку определить произведен ли запрос с помощью Ajax
     *
     * @return boolean
     */
    public static function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }
}

?>
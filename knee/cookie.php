<?php
/*
 * Куки
 */

namespace Knee;

class Cookie
{
    /**
     * Получение значения кука
     *
     * @param string $name
     * @return string|null
     */
    public static function get($name)
    {
        return (static::exists($name)) ? static::parse($_COOKIE[$name]) : null;
    }

    /**
     * Получение значения нешифрованого кука
     *
     * @param string $name
     * @return string|null
     */
    public static function getRaw($name)
    {
        return (static::exists($name)) ? $_COOKIE[$name] : null;
    }

    /**
     * Установка куков
     */
    public static function set($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
    {
        return static::setRaw($name, (static::hash($value).'+'.$value), $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Установка куков
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     * @return string|null
     */
    public static function setRaw($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
    {
        if ((int)$expire != 0) {
            $expire = Time::now() + Time::relative($expire);
        } else {
            $expire = 0;
        }

        $success = setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        if ($success) {
            $_COOKIE[$name] = $value;
        }

        return $success;
    }

    /**
     * Удаление куков
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     * @return string|null
     */
    public static function del($name, $path = '/', $domain = null, $secure = false, $httponly = false)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        return setcookie($name, null, (Time::now() - Time::day(1)), $path, $domain, $secure, $httponly);
    }

    /**
     * Проверка существования
     *
     * @param string $name
     * @return boolean
     */
    public static function exists($name)
    {
        return (array_key_exists($name, $_COOKIE)) ? true : false;
    }

    /**
     * Получаем хэш по ключу
     *
     * @param string $value
     * @return string
     */
    protected static function hash($value)
    {
        return hash_hmac('md5', $value, Config::get('main.token'));
    }

    /**
     * Парсим значение кука, отделяя и проверяя секретный ключ
     *
     * @param string $value
     * @return string|null
     */
    protected static function parse($value)
    {
        $segments = explode('+', $value);

        if (count($segments) == 1) {
            return null;
        }

        $hash = array_shift($segments);
        $value = implode('+', $segments);

        if ($hash == static::hash($value)) {
            return $value;
        }

        return null;
    }
}

<?php
/*
 * Генерация и проверка высокостойких хэшей
 */

namespace Knee;

class Hash
{
    /**
     * Генерация хэша
     *
     * @param string $value
     * @return string
     */
    public static function generate($value)
    {
        $salt = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789', 8)), 0, 40);
        $salt = substr(str_replace(array('+','/','='), '', base64_encode($salt)), 0 , 22);

        return crypt($value, '$2a$08$'.$salt);
    }

    /**
     * Проверка хэша
     *
     * @param string $value
     * @param string $hash
     * @return boolean
     */
    public static function verify($value, $hash)
    {
        return (crypt($value, $hash) === $hash) ? true : false;
    }
}

?>
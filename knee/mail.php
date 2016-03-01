<?php
/*
 * Отправка почты
 */

namespace Knee;
use Closure;

class Mail
{
    /**
     * Mail Maker (конструктор)
     */
    public static function maker()
    {
        return (new \Knee\Mail\Maker());
    }
}

?>
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
     *
     * @return \Knee\Mail\Maker
     */
    public static function maker()
    {
        return (new \Knee\Mail\Maker());
    }
}

?>
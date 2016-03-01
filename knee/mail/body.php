<?php
/*
 * Обратка тела письма
 */

namespace Knee\Mail;

class Body
{
    /**
     * Тело письма
     */
    private $message = '';

    /**
     * Конструктор
     */
    public function __construct() {}

    /**
     * Принимает тело письма
     */
    public function message($message)
    {
        $this->message = $message;
    }

    /**
     * Возвращает тело письма
     */
    public function body()
    {
        return $this->encodeBody($this->message);
    }

    /**
     * Кодирует тело письма
     */
    private function encodeBody($str)
    {
        return chunk_split(base64_encode($str), 74, "\r\n");
    }
}

?>
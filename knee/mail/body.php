<?php
/*
 * Обратка тела письма
 */

namespace Knee\Mail;

class Body
{
    /**
     * Тело письма
     *
     * @var string
     */
    protected $message = '';

    /**
     * Принимает тело письма
     *
     * @param string $message
     */
    public function message($message)
    {
        $this->message = $message;
    }

    /**
     * Возвращает тело письма
     *
     * @return string
     */
    public function body()
    {
        return $this->encodeBody($this->message);
    }

    /**
     * Кодирует тело письма
     *
     * @param string $str
     * @return string
     */
    protected function encodeBody($str)
    {
        return chunk_split(base64_encode($str), 74, "\r\n");
    }
}

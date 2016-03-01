<?php
/*
 * Сырое SQL выражения для конструктора запросов (RAW)
 */

namespace Knee\DataBase;

class Expression
{
    /**
     * Содержит выражение
     *
     * @var string
     */
    protected $expression;

    /**
     * Создает выражение
     *
     * @param string $expression - SQL выражение
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    /**
     * Возвращает выражение
     *
     * @return string
     */
    public function get()
    {
        return $this->expression;
    }

    /**
     * Возвращает выражение ввиде строки
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->get();
    }

}

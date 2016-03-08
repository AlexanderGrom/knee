<?php
/*
 * Групировка сообщений
 */

namespace Knee\Message;

class Group
{
    /**
     * Массив объектов контейнеров сообщений
     *
     * @var array
     */
    protected $containers = array();

    /**
     * Активный контейнер сообщений
     *
     * @var stdClass
     */
    protected $container = null;

    /**
     * Создание нового контейнера для сообщений
     *
     * @return boolean
     */
    public function start()
    {
        $this->container = new \stdClass();
        $this->container->messages = array();

        $this->containers[] = $this->container;
        return true;
    }

    /**
     * Добавление сообщения
     *
     * @param string $value - сообщение
     */
    public function set($value = '')
    {
        if (is_object($this->container)) {
            $this->container->messages[] = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение сообщений в виде текста
     *
     * @return string
     */
    public function getText()
    {
        if (is_object($this->container)) {
            return implode(PHP_EOL, $this->container->messages);
        } else {
            return '';
        }
    }

    /**
     * Получение сообщений в виде массива
     *
     * @return array
     */
    public function getList()
    {
        if (is_object($this->container)) {
            return $this->container->messages;
        } else {
            return array();
        }
    }

    /**
     * Получение сообщений в виде данных в формате JSON
     *
     * @return string
     */
    public function getJSON()
    {
        if (is_object($this->container)) {
            return json_encode($this->container->messages);
        } else {
            return json_encode(array());
        }
    }

    /**
     * Получение сообщений в виде данных в формате HTML
     *
     * @return string
     */
    public function getHTML()
    {
        if (is_object($this->container)) {
            $html = "<ul>";
            foreach ($this->container->messages as $value) {
                $html .= "<li>".$value."</li>";
            }
            $html .= "</ul>";

            return $html;
        } else {
            return '';
        }
    }

    /**
     * Получение кол-ва сообщений
     *
     * @return int
     */
    public function count()
    {
        if (is_object($this->container)) {
            return count($this->container->messages);
        } else {
            return 0;
        }
    }

    /**
     * Получение уровня контейнера сообщений
     *
     * @return int
     */
    public function level()
    {
        return count($this->containers);
    }

    /**
     * Удаление всех сообщений в контейнере
     *
     * @return boolean
     */
    public function clear()
    {
        if (is_object($this->container)) {
            $this->container->messages = array();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Закрытие последнего контейнера
     *
     * @return boolean
     */
    public function end()
    {
        if (count($this->containers) > 0) {
            array_splice($this->containers, -1);
            $this->container = (count($this->containers) > 0) ? end($this->containers) : null;
            return true;
        } else {
            return false;
        }
    }
}

<?php
/*
 * Добавление данных к шаблону
 */

namespace Knee\View;

class Make
{
    /**
     * Массив замен
     *
     * @var array
     */
    protected $data = array();

    /**
     * Путь к шаблону
     *
     * @var string
     */
    protected $path = null;

    /**
     * Конструктор
     *
     * @param string $path - путь к шаблону
     * @param array $data - данные для шаблона
     */
    public function __construct($path, $data)
    {
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Добавление данных data
     *
     * @param array $data - данные для шаблона
     */
    public function add($data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Очистка данных
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * Добавление данные в шаблон методом with
     *
     * @param string $name - ключ
     * @param mixed $value - значение
     *
     * @return $this
     */
    public function with($name, $value)
    {
        $segments = explode('.', $name);

        $diff = array_diff($segments, array(''));
        if ((count($segments) - count($diff)) != 0) {
            return $this;
        }

        $data = &$this->data;
        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $data) OR !is_array($data[$segment])) {
                $data[$segment] = array();
            }
            $data =& $data[$segment];
        }

        $data = $value;

        return $this;
    }

    /**
     * Добавляем данные в шаблон используя магические методы
     *
     * @param string $name - свойство объекта (ключ)
     * @param mixed $value - значение
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Получаем данные
     *
     * @param string $name - свойство объекта (ключ)
     * @return string|null
     */
    public function __get($name)
    {
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : null;
    }

    /**
     * Проверяем данные на доступность
     *
     * @param string $name - ключ
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Удаляем данные
     *
     * @param string $name - ключ
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Ручная компиляция
     * Неявная компиляция происходит в методе __toString()
     *
     * @return string
     */
    public function compile()
    {
        $compile = new \Knee\View\Compile($this->path, $this->data);

        return (string) $compile->result();
    }

    /**
     * Строковое представление объекта
     * Производит неявную компиляцию
     *
     * @return string
     */
    public function __toString()
    {
        return $this->compile();
    }
}

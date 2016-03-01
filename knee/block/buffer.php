<?php
/*
 * Реализация буфера блоков
 */

namespace Knee\Block;

class Buffer
{
    /**
     * Массив с блоками
     */
    private $blocks = array();

    /**
     * Массив с позициями блоков
     */
    private $positions = array();

    /**
     * Добавление нового блока в буфер
     */
    public function add($path, $position = null)
    {
        if (array_key_exists($path, $this->blocks)) {
            return false;
        }

        $parse_path = explode(".", $path);

        $diff = array_diff($parse_path, array(''));
        if ((count($parse_path) - count($diff)) != 0) {
            return false;
        }

        foreach ($parse_path as $value) {
            if (mb_substr($value, 0, 1) == '_') {
                return false;
            }
        }

        $file_path = ROOT_PATH.'/app/blocks/'.implode("/", $parse_path).'.php';

        if (is_file($file_path)) {
            require_once($file_path);

            $class = mb_ucwords(implode("_", $parse_path))."_Block";

            if (!is_null($position)) {
                $this->positions[$path] = $position;
            }

            $this->blocks[$path] = new $class();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Смена позиции блока
     */
    public function pos($path, $position)
    {
        if (array_key_exists($path, $this->blocks)) {
            $this->positions[$path] = $position;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение конкрутного блока
     */
    public function get($path)
    {
        if (array_key_exists($path, $this->blocks)) {
            return $this->blocks[$path];
        } else {
            return false;
        }
    }

    /**
     * Сброс буфера
     */
    public function flush()
    {
        $result = array();
        $positions = $this->positions;
        $blocks = array_diff_key($this->blocks, $this->positions);

        $index = 1;
        while (list($path, $object) = each($blocks)) {
            if (!in_array($index, $positions)) {
                $positions[$path] = $index;
                unset($blocks[$path]);
            } else {
                reset($blocks);
            }

            $index++;
        }

        asort($positions);

        foreach ($positions as $path => $index) {
            $result[] = (string)$this->blocks[$path]->__toString();
        }

        return implode("\n", $result);
    }

    /**
     * Удаление конкретного блока из буфера
     */
    public function del($path)
    {
        if (array_key_exists($path, $this->blocks)) {
            if (array_key_exists($path, $this->positions)) {
                unset($this->positions[$path]);
            }

            unset($this->blocks[$path]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Очистка буфера блоков
     */
    public function clear()
    {
        $this->positions = array();
        $this->blocks = array();
        return true;
    }

    /**
     * Неявный flush
     */
    public function __toString()
    {
        return $this->flush();
    }
}

?>
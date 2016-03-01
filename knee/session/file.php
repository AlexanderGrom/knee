<?php
/*
 * Сессии (хранение в файлах)
 */

namespace Knee\Session;
use Config, Cookie, Time, Str;

class File
{
    /**
     * Массив с данными сессии
     */
    private $session_data = array();

    /**
     * Указатель на открытый файл с данными сессии
     */
    private $session_resource = null;

    /**
     * Путь к хранилищу файлов сессий
     */
    private $session_storage = null;

    /**
     * ID сессии
     */
    private $session_id = null;

    /**
     * Старт сессии
     */
    public function __construct()
    {
        $this->session_storage = ROOT_PATH.'/cache/sessions';
        $this->session_id = $this->session_id();
        $this->session_resource = fopen($this->storage($this->session_id), 'a+');

        $this->lock();

        $this->session_data = $this->read();
    }

    /**
     * Получение или создание нового ID сессии
     */
    private function session_id()
    {
        $session_id = Cookie::get('session_id');

        do {
            $error = true;

            if (is_null($session_id)) break;
            if (mb_strlen($session_id) != 32) break;
            if (preg_match('#^[a-z0-9]+$#u', $session_id) == 0) break;

            $error = false;
        } while(false);

        if ($error)
        {
            do {
                $session_id = Str::hash(32);
            }
            while(is_file($this->storage($session_id)));
        }

        $this->cookie($session_id);

        return $session_id;
    }

    /**
     * Путь к хранилищу данных
     */
    private function storage($session_id = '')
    {
        return $this->session_storage."/".$session_id;
    }

    /**
     * Чтение данных из файла сессии
     */
    private function read()
    {
        $data = "";
        while (($buffer = fgets($this->session_resource, 4096)) !== false) {
            $data .= $buffer;
        }

        $session_data = @unserialize($data);

        return (is_array($session_data)) ? $session_data : array();
    }

    /**
     * Запись данных в файл сессии
     */
    private function write()
    {
        $session_data = @serialize($this->session_data);

        $session_data = (is_string($session_data)) ? $session_data : @serialize(array());

        ftruncate($this->session_resource, 0);
        fputs($this->session_resource, $session_data);

        return true;
    }

    /**
     * Добавление данных сессии
     */
    public function set($key, $value)
    {
        $this->session_data[$key] = $value;

        return true;
    }

    /**
     * Получение значения сессии по ключу
     */
    public function get($key)
    {
        return ($this->exists($key)) ? $this->session_data[$key] : null;
    }

    /**
     * Удаление данных сессии
     */
    public function del($key)
    {
        if ($this->exists($key)) {
            unset($this->session_data[$key]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка существования данных в сессии
     */
    public function exists($key)
    {
        return (array_key_exists($key, $this->session_data)) ? true : false;
    }

    /**
     * Обновление данных сессии
     */
    private function update()
    {
        $this->write();
    }

    /**
     * Удаление сессии
     */
    public function destroy()
    {
        $this->session_data = array();

        return true;
    }

    /**
     * Блокировка
     */
    private function lock()
    {
        return flock($this->session_resource, LOCK_EX);
    }

    /**
     * Снятие блокировки
     */
    private function unlock()
    {
        return flock($this->session_resource, LOCK_UN);
    }

    /**
     * Установка cookie
     */
    private function cookie($session_id)
    {
        extract(Config::get('session'));

        Cookie::set('session_id', $session_id, $expire, $path, $domain, $secure, true);
    }

    /**
     * Удаление устаревших файлов сессии через лотерею
     */
    private function sweep()
    {
        if (in_array(mt_rand(1, 100), array(7,23,45,66,81))) {
            $expire = Config::get('session.expire');
            $expire = Time::now() - Time::relative($expire);

            $files = glob($this->storage('*'));

            if ($files !== false) {
                foreach ($files as $file) {
                    if (is_file($file) AND filemtime($file) < $expire) {
                        @unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Сохранение и чистка сессии
     */
    public function save()
    {
        $this->update();
        $this->unlock();

        $this->sweep();
    }
}

?>
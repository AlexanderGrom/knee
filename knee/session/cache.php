<?php
/*
 * Сессии (хранение в memcache)
 */

namespace Knee\Session;
use Config, Cookie, Time, Str;

class Cache
{
    /**
     * Массив с данными сессии
     */
    protected $session_data = array();

    /**
     * ID сессии
     */
    protected $session_id = null;

    /**
     * Старт сессии
     */
    public function __construct()
    {
        $this->session_id = $this->session_id();

        $this->lock();

        $this->session_data = $this->read();
    }

    /**
     * Получение или создание нового ID сессии
     */
    protected function session_id()
    {
        $session_id = Cookie::get('session_id');

        do {
            $error = true;

            if (is_null($session_id)) break;
            if (mb_strlen($session_id) != 32) break;
            if (preg_match('#^[a-z0-9]+$#s', $session_id) == 0) break;

            $error = false;
        } while(false);

        if ($error) {
            do {
                $session_id = Str::hash(32);
            }
            while(\Knee\Cache::exists("ses_".$session_id));
        }

        $this->cookie($session_id);

        return $session_id;
    }

    /**
     * Чтение данных сессии
     */
    protected function read()
    {
        $session_data = \Knee\Cache::get("ses_".$this->session_id);

        return (is_array($session_data)) ? $session_data : array();
    }

    /**
     * Запись данных сессии
     */
    protected function write()
    {
        extract(Config::get('session'));

        \Knee\Cache::set("ses_".$this->session_id, $this->session_data, array(), $expire);

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
    protected function update()
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
    protected function lock()
    {
        $lock_key = "ses_lock_".$this->session_id;
        $lock_time = 7;

        while(\Knee\Cache::add($lock_key, $this->session_id, array(), $lock_time) === false)
        {
            usleep(250);
        }
    }

    /**
     * Снятие блокировки
     */
    protected function unlock()
    {
        \Knee\Cache::del("ses_lock_".$this->session_id);
    }

    /**
     * Установка cookie
     */
    protected function cookie($session_id)
    {
        extract(Config::get('session'));

        Cookie::set('session_id', $session_id, $expire, $path, $domain, $secure, true);
    }

    /**
     * Сохранение сессии
     */
    public function save()
    {
        $this->update();
        $this->unlock();
    }
}

?>
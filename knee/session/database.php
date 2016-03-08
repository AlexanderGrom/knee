<?php
/*
 * Сессии (хранение в базе данных)
 */

namespace Knee\Session;
use Config, Cookie, Time, Str, DB;

class DataBase
{
    /**
     * Массив с данными сессии
     */
    protected $session_data = array();

    /**
     * Таблица с данными
     */
    protected $session_storage = null;

    /**
     * ID сессии
     */
    protected $session_id = null;


    /**
     * Старт сессии
     */
    public function __construct()
    {
        $this->session_storage = Config::get('session.table');
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
            if (preg_match('#^[a-z0-9]+$#u', $session_id) == 0) break;

            $error = false;
        } while(false);

        if ($error) {
            do {
                $session_id = Str::hash(32);
            }
            while($this->storage($session_id) !== false);
        }

        $this->cookie($session_id);

        return $session_id;
    }

    /**
     * Таблица с сессиями
     */
    public function storage($session_id)
    {
        return DB::query("SELECT * FROM `".$this->session_storage."` WHERE session_id=:id LIMIT 1", array('id'=>$session_id))->getObject();
    }

    /**
     * Чтение данных сессии
     */
    protected function read()
    {
        $result = $this->storage($this->session_id);

        if ($result !== false) {
            $session_data = @unserialize($result->session_data);
        } else {
            $session_data = array();
        }

        return (is_array($session_data)) ? $session_data : array();
    }

    /**
     * Запись данных сессии
     */
    protected function write()
    {
        $session_data = @serialize($this->session_data);

        $session_data = (is_string($session_data)) ? $session_data : @serialize(array());

        if ($this->storage($this->session_id) === false) {
            $DBData = array();
            $DBData['session_id'] = $this->session_id;
            $DBData['session_create_date'] = Time::now();
            $DBData['session_update_date'] = Time::now();
            $DBData['session_data'] = $session_data;

            DB::query("INSERT INTO `".$this->session_storage."` SET
            session_id = :session_id,
            session_create_date = :session_create_date,
            session_update_date = :session_update_date,
            session_data = :session_data",
            $DBData);
        } else {
            $DBData = array();
            $DBData['session_id'] = $this->session_id;
            $DBData['session_update_date'] = Time::now();
            $DBData['session_data'] = $session_data;

            DB::query("UPDATE `".$this->session_storage."` SET
            session_update_date = :session_update_date,
            session_data = :session_data WHERE session_id = :session_id LIMIT 1",
            $DBData);
        }

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
        $lock_time = ini_get('max_execution_time');

        $data = array();
        $data['lock_key'] = $this->session_id;
        $data['lock_time'] = 7;

        DB::query("SELECT GET_LOCK(:lock_key, :lock_time)", $data);
    }

    /**
     * Снятие блокировки
     */
    protected function unlock()
    {
        DB::query("SELECT RELEASE_LOCK(:lock_key)", array('lock_key'=>$this->session_id));
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
     * Удаление устаревших файлов сессии через лотерею
     */
    protected function sweep()
    {
        if (in_array(mt_rand(1, 100), array(7,23,45,66,81))) {
            $expire = Config::get('session.expire');
            $expire = Time::now() - Time::relative($expire);

            DB::query("DELETE FROM `".$this->session_storage."` WHERE session_update_date < :expire", array('expire'=>$expire));
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
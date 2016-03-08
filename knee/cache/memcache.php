<?php
/*
 * Работа с кэшем используя библиотеку memcache
 */

namespace Knee\Cache;

use Config, Error, Lang, Time;

class Memcache
{
    /**
     * Коннект к memcached
     *
     * @var object
     */
    protected $connection = null;

    /**
     * Ключ-прификс
     *
     * @var string
     */
    protected $token = null;

    /**
     * Подключение
     */
    public function __construct()
    {
        $this->token = Config::get('cache.token');

        if (!class_exists('Memcache')) {
            Error::e503(Lang::get('system.cache.nodriver'));
        }

        return $this->connection();
    }

    /**
     * Подключение
     *
     * @return Memcached
     */
    protected function connection()
    {
        $memcached = new \Memcache;

        $servers = Config::get('cache.servers');

        foreach ($servers as $server) {
            $memcached->addServer($server['host'], $server['port'], $server['weight']);
        }

        return $memcached;
    }

    /**
     * Получение соединения
     *
     * @return Memcached
     */
    public function connect()
    {
        if (!is_object($this->connection)) {
            $this->connection = $this->connection();
        }

        return $this->connection;
    }

    /**
     * Добавление данных в кэш с перезаписью зачений
     *
     * @param string $key
     * @param string $value
     * @param array $tags
     * @param int $expire
     * @return boolean
     */
    public function set($key, $value, $tags = array(), $expire = 0)
    {
        $connect = $this->connect();

        if (!is_object($connect)) {
            return false;
        }

        if ((int)$expire != 0) {
            $expire = Time::now() + Time::relative($expire);
        } else {
            $expire = 0;
        }

        $tags_values = array();
        if (count($tags) > 0) {
            $microtime = microtime(true);

            foreach ($tags as $tag) {
                if (($tag_value = $connect->get($this->token.'tag.'.$tag)) !== false) {
                    $tags_values[$tag] = $tag_value;
                } else {
                    $tags_values[$tag] = $microtime;
                    $connect->add($this->token.'tag.'.$tag, $microtime, false, 0);
                }
            }
        }

        $values = array();
        $values['value'] = $value;
        if (count($tags_values) > 0) {
            $values['tags'] = $tags_values;
        }

        return $connect->set($this->token.'var.'.$key, $values, false, $expire);
    }

    /**
     * Получение значения из кэша
     *
     * @param string $key
     * @return string|null
     */
    public function get($key)
    {
        $connect = $this->connect();

        if (!is_object($connect)) {
            return null;
        }

        if (($values = $connect->get($this->token.'var.'.$key)) !== false) {
            if (!is_array($values)) {
                return null;
            }

            if (isset($values['tags'])) {
                foreach ($values['tags'] as $tag_key => $tag_val) {
                    if ($tag_val != $connect->get($this->token.'tag.'.$tag_key)) {
                        return null;
                    }
                }
            }

            return (isset($values['value'])) ? $values['value'] : null;
        }
        else {
            return null;
        }
    }

    /**
     * Удаление данных из кэша
     *
     * @param string $key
     * @return boolean
     */
    public function del($key)
    {
        $connect = $this->connect();

        if (!is_object($connect)) {
            return false;
        }

        return $connect->delete($this->token.'var.'.$key);
    }

    /**
     * Проверка существования данных в кэше, учитывает обнуление тегов
     *
     * @param string $key
     * @return boolean
     */
    public function exists($key)
    {
        return ($this->get($key) !== null) ? true : false;
    }

    /**
     * "Удалить" все объекты или обнулить тег
     *
     * @param array $tags
     * @return boolean
     */
    public function clear($tags = array())
    {
        $connect = $this->connect();

        if (!is_object($connect)) {
            return false;
        }

        if (count($tags) > 0) {
            $microtime = microtime(true);

            foreach ($tags as $tag) {
                $connect->replace($this->token.'tag.'.$tag, false, $microtime, 0);
            }

            return true;
        } else {
            return $connect->flush();
        }
    }

    /**
     * Добавление данных в кэш (для внутренних нужд)
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @return boolean
     */
    public function add($key, $value, $expire = 0)
    {
        $connect = $this->connect();

        if (!is_object($connect)) {
            return false;
        }

        if ((int)$expire != 0) {
            $expire = Time::now() + $expire;
        } else {
            $expire = 0;
        }

        $tags_values = array();

        if (count($tags) > 0) {
            $microtime = microtime(true);

            foreach ($tags as $tag) {
                if (($tag_value = $connect->get($this->token.'tag.'.$tag)) !== false) {
                    $tags_values[$tag] = $tag_value;
                } else {
                    $tags_values[$tag] = $microtime;
                    $connect->add($this->token.'tag.'.$tag, $microtime, false, 0);
                }
            }
        }

        $values = array();
        $values['value'] = $value;
        if (count($tags_values) > 0) {
            $values['tags'] = $tags_values;
        }

        return $connect->add($this->token.'var.'.$key, $values, false, $expire);
    }
}

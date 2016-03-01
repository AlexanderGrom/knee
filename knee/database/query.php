<?php
/*
 * Работа с запросами
 */

namespace Knee\DataBase;
use PDO, PDOException, Config, Error;

class Query
{
    /**
     * Объект PDO
     *
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Кол-во найденых плейсхолдеров
     *
     * @var int
     */
    protected $placeholders_count = 0;

    /**
     * Массив-карта найденых плейсхолдеров
     *
     * @var array
     */
    protected $placeholders_map = array();

    /**
     * Массив переданых плейсхолдеров
     */
    protected $placeholders_data = array();

    /**
     * Конструктор
     *
     * @param PDO $pdo - объект PDO
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Получить объект PDO
     *
     * @return PDO
     */
    public function pdo()
    {
        return $this->pdo;
    }

    /**
     * Запрос к базе данных
     *
     * @param string $sql - запрос SQL
     * @param array $data - данные
     * @return \Knee\DataBase\Statement
     */
    public function query($sql, $data = array())
    {
        $pdo = $this->pdo();

        $this->placeholders_count = 0;
        $this->placeholders_map = array();
        $this->placeholders_data = $this->placeholders_normalize($data);

        $sql = preg_replace_callback("#:(?<name>[a-zA-Z0-9\_]+)#isu", array($this, 'query_placeholder_build'), $sql);

        if (count($this->placeholders_map) == 0) {
            foreach ($data as $key => $value) {
                if (is_int($key)) {
                    $this->placeholders_map[$key] = $value;
                }
            }
        }

        try {
            $sth = $pdo->prepare($sql);
            $sth->execute($this->placeholders_map);

            return new \Knee\DataBase\Statement($pdo, $sth);
        }
        catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Строим плейсхолдеры с разными именами (избавляемся от совпадения имен)
     *
     * @param array $match
     * @return string
     */
    protected function query_placeholder_build($match)
    {
        $placeholder = $match['name'];
        $suffix = ++$this->placeholders_count;

        $placeholder_suffix = $placeholder.'__'.$suffix;

        if (array_key_exists($placeholder, $this->placeholders_data)) {
            $this->placeholders_map[$placeholder_suffix] = $this->placeholders_data[$placeholder];
        }

        return ':'.$placeholder_suffix;
    }

    /**
     * Приводим имена плейсхолдеров в массиве data к нормальной форме (убираем двоеточие в ключах)
     *
     * @param array $data
     * @return array
     */
    protected function placeholders_normalize($data)
    {
        $DBData = array();
        foreach ($data as $key => $value) {
            $key = (substr($key, 0, 1) == ':') ? substr($key, 1) : $key;
            $DBData[$key] = $value;
        }

        return $DBData;
    }

    /**
     * Старт транзации
     *
     * @param Closure $callback
     */
    public function transactionStart($callback)
    {
        try {
            $this->pdo()->beginTransaction();
        }
        catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Откат транзации
     *
     * @param Closure $callback
     */
    public function transactionBack($callback)
    {
        try {
            $this->pdo()->rollBack();
        }
        catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Завершение транзации и сохранение изменений
     *
     * @param Closure $callback
     */
    public function transactionEnd($callback)
    {
        try {
            $this->pdo()->commit();
        }
        catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Конструктор запросов (вход)
     *
     * @param string $table
     * @return Builder
     */
    public function table($name)
    {
        $table = is_array($name) ? $name : func_get_args();

        return (new \Knee\DataBase\Builder($this))->table($table);
    }

    /**
     * Создает сырое выражение
     *
     * @param string expression - SQL выражение
     * @return \Knee\DataBase\Expression
     */
    public function raw($expression)
    {
        return new \Knee\DataBase\Expression($expression);
    }

    /**
     * Экранирует спец. символы для безопастного применения в запросе
     *
     * @param string $str - сырая строка
     * @return string
     */
    public function escape($str)
    {
        return $this->pdo()->quote($str);
    }

    /**
     * Сообщение об ошибке
     *
     * @param string $msg - сообщение
     */
    public function error($msg)
    {
        $msg = (Config::get('database.error')) ? $msg : Lang::get('system.db.error');
        Error::e503($msg);
    }
}

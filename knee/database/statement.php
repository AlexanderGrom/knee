<?php
/*
 * Работа с базой данный (DBStatement)
 *
 * Переобределяет стандартный DBStatement
 */

namespace Knee\DataBase;
use PDO;

class Statement
{
    /**
     * Объект PDO
     *
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Объект PDOStatement
     *
     * @var PDOStatement
     */
    protected $sth = null;

    /**
     * Конструктор
     *
     * @param PDO $pdo - объект PDO
     * @param PDOStatement $sth - объект PDOStatement
     */
    public function __construct($pdo, $sth)
    {
        $this->pdo = $pdo;
        $this->sth = $sth;
    }

    /**
     * Получить следующую строку из набора в виде ассоциативного массива
     *
     * @return array
     */
    public function getArray()
    {
        return $this->sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Получить следующую строку из набора в виде списка
     *
     * @return array
     */
    public function getList()
    {
        return $this->sth->fetch(PDO::FETCH_NUM);
    }

    /**
     * Получить следующую строку из набора в виде объекта
     *
     * @return object
     */
    public function getObject()
    {
        return $this->sth->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Возвращает массив всех выбранных строк в виде ассоциативного массива
     *
     * @return array
     */
    public function getArrayAll()
    {
        return $this->sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает массив всех выбранных строк в виде списка
     *
     * @return array
     */
    public function getListAll()
    {
        return $this->sth->fetchAll(PDO::FETCH_NUM);
    }

    /**
     * Возвращает массив всех выбранных строк в виде объекта
     *
     * @return array
     */
    public function getObjectAll()
    {
        return $this->sth->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Получить кол-во строк затронутых запросом
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->sth->rowCount();
    }

    /**
     * Получить ID последней вставленной строки
     *
     * @return int
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}

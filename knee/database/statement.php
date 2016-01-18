<?php
/*
 * Knee framework
 * Назначение: Работа с базой данный (DBStatement)
 */

namespace Knee\DataBase;
use PDO;

class Statement
{
	/**
	 * Объект PDO
	 */
	private $pdo = null;

	/**
	 * Объект PDOStatement
	 */
	private $sth = null;

	/**
	 * Конструктор
	 */
	public function __construct($pdo, $sth)
	{
		$this->pdo = $pdo;
		$this->sth = $sth;
	}

	/**
	 * Получить следующую строку из набора в виде ассоциативного массива
	 */
	public function getArray()
	{
		return $this->sth->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Получить следующую строку из набора в виде списка
	 */
	public function getList()
	{
		return $this->sth->fetch(PDO::FETCH_NUM);
	}

	/**
	 * Получить следующую строку из набора в виде объекта
	 */
	public function getObject()
	{
		return $this->sth->fetch(PDO::FETCH_OBJ);
	}

	/**
	 * Возвращает массив всех выбранных строк в виде ассоциативного массива
	 */
	public function getArrayAll()
	{
		return $this->sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * ПВозвращает массив всех выбранных строк в виде списка
	 */
	public function getListAll()
	{
		return $this->sth->fetchAll(PDO::FETCH_NUM);
	}

	/**
	 * Возвращает массив всех выбранных строк в виде объекта
	 */
	public function getObjectAll()
	{
		return $this->sth->fetchAll(PDO::FETCH_OBJ);
	}

	/**
	 * Получить кол-во строк затронутых запросом
	 */
	public function rowCount()
	{
		return $this->sth->rowCount();
	}

	/**
	 * Получить ID последней вставленной строки
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}
}

?>
<?php
/*
 * Доступ к базе данный
 */

namespace Knee;
use PDO, PDOException;

class DataBase
{
    /**
     * Соединения с базой
     */
    protected static $connections = array();

    /**
     * Подключение к базе данных
     *
     * @return \Knee\DataBase\Query
     */
    public static function connection($connect = null)
    {
        $connect = (!is_null($connect)) ? $connect : 'default';

        if (array_key_exists($connect, static::$connections)) {
            return static::$connections[$connect];
        }

        try {
            $config = Config::get('database.connections.'.$connect);

            if (!is_array($config)) {
                throw new PDOException();
            }

            extract($config);

            $dsn = "mysql:host=".$host.";port=".$port.";dbname=".$database;

            $pdo = new PDO($dsn, $username, $password, array());

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $pdo->query("SET NAMES '".$charcter."'");

            $DBQuery = new \Knee\DataBase\Query($pdo);
            static::$connections[$connect] = $DBQuery;

            return static::$connections[$connect];
        }
        catch (PDOException $e) {
            Error::e503(Lang::get('system.db.noconnect'));
        }
    }

    /**
     * Магический callStatic
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::connection('default'), $method), $parameters);
    }
}

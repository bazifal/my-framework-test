<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 15:46
 */

namespace core;

use PDO;
use PDOStatement;

/**
 * Class DbConnection
 * @package core
 */
class DbConnection extends Component
{
    /**
     * @var string
     */
    public $dsn;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var PDO
     */
    protected $connection;

    /**
     * @inheritdoc
     */
    public function onConstruct()
    {
        $this->connection = new PDO($this->dsn, $this->username, $this->password);
        $this->query("set names utf8");
    }

    /**
     * bConnection destructor.
     */
    public function __destruct()
    {
        unset($this->connection);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    protected function prepare(string $sql, array $params = []): PDOStatement
    {
        $st = $this->connection->prepare($sql);
        foreach ($params as $key => $val) {
            $st->bindValue($key, $val, is_numeric($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        return $st;
    }

    /**
     * выполняет SQL запрос
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function exec(string $sql, array $params = []): bool
    {
        $st = $this->prepare($sql, $params);
        return $st->execute();
    }

    /**
     * выполняет SQL запрос и возвращает результат в виде ассоциативного массива
     * @param $sql
     * @param $params
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        $st = $this->prepare($sql, $params);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}
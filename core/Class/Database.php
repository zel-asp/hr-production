<?php

namespace Core;

use PDO;

class Database
{
    private $statement;
    private $connection;

    public function __construct($config, $username = null, $password = null)
    {
        // Use the username and password from config if not provided
        $username = $username ?? $config['username'];
        $password = $password ?? $config['password'];

        $dsn = 'mysql:' . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Add this for better error reporting
        ]);
    }

    public function query($query, $param = [])
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($param);
        return $this;
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

    public function find()
    {
        return $this->statement->fetchAll();
    }

    public function fetch_one()
    {
        return $this->statement->fetch();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function fetchOrAbort()
    {
        try {
            $result = $this->find();
            if (!$result) {
                abort(401);
            } else {
                return $result;
            }
        } catch (\Throwable $error) {
            echo $error->getMessage();
        }
    }

    public function count()
    {
        return $this->statement->rowCount();
    }
}
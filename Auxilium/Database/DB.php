<?php

namespace Auxilium\Database;

use Exception;
use Auxilium\Support\ArrayHelper as Arr;
use Auxilium\Support\Config;
use mysqli;

class DB
{
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private int $port;
    private mysqli $connection;

    public function __construct()
    {
        $dbData = Config::dbData();

        $this->host = Arr::get($dbData, 'host');
        $this->database = Arr::get($dbData, 'database');
        $this->username = Arr::get($dbData, 'username');
        $this->password = Arr::get($dbData, 'password');
        $this->port = Arr::get($dbData, 'port');
    }

    public function execute(string $query)
    {
        if ($query == null)
            throw new Exception('QUERY IS NULL');

        if ($this->connection == null) {
            if (!$this->open())
                throw new Exception('DB OPEN ERROR');
        }

        try {
            $result = $this->connection->query($query);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        // DEBUG CODE

        echo '<pre>';
        var_dump($this->connection->error_list);
        echo '</pre>';

        // DEBUG CODE

        try {
            $result = $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        $this->close();

        return $result;
    }

    public function open(): bool
    {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port,
            );
        } catch (Exception $exception) {
            echo $exception->getMessage();
            return false;
        }

        return true;
    }

    private function close(): bool
    {
        try {
            $this->connection->close();
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function escape(string $param): string
    {
        return $this->connection->escape_string($param);
    }
}
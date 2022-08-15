<?php

namespace Core;

use Exception;
use PDO;
use PDOException;

class DatabaseConnection
{
    protected static $instannce;
    protected $connect;

    private string $host = 'localhost';
    private string $user = 'root';
    private string $password = '';
    private string $name = 'mvc';

    private function __construct()
    {
        try {
            $this->connect = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->connect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    private function __clone() {
        throw new Exception("Can't clone a singleton");
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Can't unserialize a singleton");
    }

    /**
     * @throws Exception
     */
    public function __sleep()
    {
        throw new Exception("Can't serialize a singleton");
    }

    public static function getInstance(): DatabaseConnection
    {
        if (is_null(self::$instannce)) {
            self::$instannce = new DatabaseConnection;
        }

        return self::$instannce;
    }

    public function connect()
    {
        return $this->connect;
    }

}
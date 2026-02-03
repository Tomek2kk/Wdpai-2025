<?php

class Database {

    private static $instance = null;
    private $conn;

    private function __construct() {

        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        $this->conn = new PDO(
            "pgsql:host=$host;dbname=$db",
            $user,
            $pass
        );

        $this->conn->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public static function getInstance() {

        if(self::$instance === null){
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(){
        return $this->conn;
    }
}

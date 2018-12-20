<?php

class DB{
    private static $_instance = null;
    private $_connection;

    private function __construct() {
        $dsn = "mysql:host=".HOST.";dbname=".DB.";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->_connection = new PDO($dsn, USERNAME, PASSWORD, $options);
        } catch (Exception $e) {
            printError($e->getMessage());
        }

    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function getConn(){
        return $this->_connection;
    }
}

?>

<?php

class DB{
    private static $_instance = null;
    private $_connection;

    private function __construct() {
        try{
            $this->_connection = new mysqli(HOST, USERNAME,PASSWORD, DB);
            if($this->_connection){
                return $this->_connection;
            }else{
                die("Error");
            }

        }catch(Exception $e){
            echo $e->getMessage();
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

<?php


class User extends Model
{
    private $table = 'user';
    //private $uniqueCol = 'email';
    public function __construct()
    {
        parent::__construct();
//        if(!$this->tableExists($this->table)){
//            printError("Table Not Exist");
//        }else{
//            printError("Table Exist");
//        }
        $this->setTable($this->table);
    }

    public function getAllUsers(){

    }
}
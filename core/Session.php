<?php
class Session
{

    public static function add($key,$value){
        $_SESSION[$key] = $value;
    }
    public static function has($key){
        if(isset($_SESSION[$key])){
            return true;
        }else{
            return false;
        }
    }
    public static function get($key){
        if(self::has($key)){
            return $_SESSION[$key];
        }else{
            return false;
        }
    }
    public static function flushGet($key){
        if(self::has($key)){
            $value = $_SESSION[$key];
            self::remove($key);
            return $value;
        }else{
            return false;
        }
    }
    public static function remove($key){

        if(self::has($key)){
            unset($_SESSION[$key]);
            //printError("Remove session");
        }
    }
    public static function destroy(){
        session_destroy();
    }
}
<?php

class Csrf
{

    public static function createToken(){
        $token = mt_rand(100000000,999999999);
        Session::add(TOKEN,$token);
        $name = TOKEN;
        echo "<input type='hidden' value='$token' name='$name' >";
    }

    public static function matchToken($formToken){
        $token = self::getToken();
        if($formToken == $token){
            Session::remove(TOKEN);
            return true;
        }else{
            return false;
        }
    }

    public static function getToken(){
        return Session::get(TOKEN);
    }
}
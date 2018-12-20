<?php

class Router
{
    private static $_GET_ROUTE_LIST = array();
    private static $_POST_ROUTE_LIST = array();

    public static function route($url){
        $hasMatch = false; 
        $hasParameter = false;
        $parameter = null;
        $controllerWithFunction = null;

        foreach(self::$_GET_ROUTE_LIST as $k=>$v){
            $controllerWithFunction = $v;

            if(strpos($k,'/?') !== false){
                $hasParameter = true;
                $k = str_ireplace('/?','',$k);
            }
            $hasMatch = self::matchUrl($k,$url);
            if($hasMatch){
                $parameter = str_replace($k,'',$url);
                break;
            }
        }

        if($hasMatch === false){
            //TODO: for post url
            foreach(self::$_POST_ROUTE_LIST as $k=>$v){
                $controllerWithFunction = $v;

                if(strpos($k,'/?') !== false){
                    $hasParameter = true;
                    $k = str_ireplace('/?','',$k);
                }
                if(self::checkIfTokenMatch() === false){
                    die();
                }
                $hasMatch = self::matchUrl($k,$url);
                if($hasMatch){
                    $parameter = str_replace($k,'',$url);
                    break;
                }
            }
        }

        if($hasMatch){
            if(is_null($controllerWithFunction)){
                printError('Controller not set in route.php');
                die();
            }
            // set controller
            $array = explode('@',$controllerWithFunction);
            $controller = $array[0];

            // set function
            $function  = isset($array[1])? $array[1] : DEFAULT_FUNCTION;
            if($hasParameter){
                $parameter = trim($parameter,'/');

                if(strpos($parameter,'/') === false){
                    $parameter = toArray($parameter);
                }else{
                    $parameter = explode('/',$parameter);
                }
            }

            $classArray = explode('/',$controller);
            $controller = $classArray[sizeof($classArray)-1];
            $class = implode(DS,$classArray);
            if(file_exists(ROOT.DS.'app'.DS.'controller'.DS.$class.'.php')){
                require_once(ROOT.DS.'app'.DS.'controller'.DS.$class.'.php');
            }else{
                printError("Class not found: $controller");
                die();
            }

            if(method_exists($controller,$function)){
                $obj = new $controller;
                if($hasParameter){
                    call_user_func_array(array($obj, $function),$parameter);
                }else{
                    call_user_func(array($obj, $function));
                }


            }else{
                printError("Function not found in class ".$controller);
                die();
            }
        }else{
            echo "404 - Not Found";
            die();
        }

    }

    static function matchUrl($route,$actualUrl){
        if(strlen($route) > strlen($actualUrl) || is_null($actualUrl)){
            return false;
        }elseif($route === $actualUrl){
            return true;
        }else{
            $urlArray = explode('/',$actualUrl);
            array_pop($urlArray);
            $removeLastPart = implode('/',$urlArray);
            return self::matchUrl($route, $removeLastPart);
        }
    }

    private static function checkIfTokenMatch(){
        if(isset($_POST[TOKEN]) && (Csrf::matchToken($_POST[TOKEN]) === true)){
            // token match
            return true;
        }else{
            printError("Token Mismatch");
            return false;

        }
    }

    private static function set($route,$controller,$type){
        if($route != '/'){
            $route = trim($route,'/');
        }
        if(array_key_exists($route,self::$_GET_ROUTE_LIST) || array_key_exists($route,self::$_POST_ROUTE_LIST)){
            printError("The Route ($route) is already present");
            die();
        }
        if($type == GET){
            self::$_GET_ROUTE_LIST[$route] = $controller;
        }elseif($type == POST){
            self::$_POST_ROUTE_LIST[$route] = $controller;
        }else{
            printError("Type $type Mismatch");
        }

    }

    public static function get($route,$controller){
        self::set($route,$controller,GET);
    }

    public static function post($route,$controller){
        self::set($route,$controller,POST);
    }

}
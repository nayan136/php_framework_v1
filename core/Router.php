<?php

class Router
{
    private static $_ROUTE_LIST = array();

    public static function route($url){

        $hasMatch = false;
        $hasParameter = false;
        $parameter = null;
        $controllerWithFunction = null;

        foreach(self::$_ROUTE_LIST as $k=>$v){
            $controllerWithFunction = $v;

            if(strpos($k,'/?') !== false){
                $hasParameter = true;
                $k = str_ireplace('/?','',$k);
            }


            $hasMatch = self::matchUrl($k,$url);
//            echo "Match :";
//            var_dump($hasMatch);
            if($hasMatch){
                $parameter = str_replace($k,'',$url);
                break;
            }
//            $length = strpos($url,$k);
//            printError($length);
//            if($length !== false){
//                $hasMatch = true;
//                break;
//            }
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

//            $functionWithParameter = (isset($array[0]))?(explode('/',$array[0])):toArray(DEFAULT_FUNCTION);
//            $function = $functionWithParameter[0];
//            printError("Function: ".$function);
//            dnd($array);
//            array_shift($array);
//            $parameter = $url;
//            dnd($parameter);

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
                printError("Controller: ".$controller);
                $obj = new $controller;
                dnd($parameter);
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

            printError("URL: ".$url);
            dnd(self::$_ROUTE_LIST);

            echo "404 - Not Found";
            die();
        }

    }

    static function matchUrl($route,$actualUrl){
        echo "<br>Route: $route|Actual URL:".$actualUrl."<br>";
        if(strlen($route) > strlen($actualUrl) || is_null($actualUrl)){
            return false;
        }elseif($route === $actualUrl){
            return true;
        }else{
            $urlArray = explode('/',$actualUrl);
            array_pop($urlArray);
            dnd($urlArray);
            $removeLastPart = implode('/',$urlArray);
            return self::matchUrl($route, $removeLastPart);
        }
    }

    public static function set($route,$controller){
        if($route != '/'){
            $route = trim($route,'/');
        }
        if(array_key_exists($route,self::$_ROUTE_LIST)){
            printError("The Route ($route) is already present");
            die();
        }

        self::$_ROUTE_LIST[$route] = $controller;
    }
}
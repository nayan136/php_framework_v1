<?php

// load configuration and helper function
require_once(ROOT.DS.'config'.DS.'config.php');
require_once(ROOT.DS.'config'.DS.'route.php');
require_once(ROOT.DS.'app'.DS.'lib'.DS.'helpers'.DS.'function.php');
require_once(ROOT.DS.'app'.DS.'lib'.DS.'helpers'.DS.'helper.php');

if(APP_DEBUG){
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

function __autoload($className){
    if(file_exists(ROOT.DS.'core'.DS.$className.'.php')){
        require_once(ROOT.DS.'core'.DS.$className.'.php');
    }elseif(file_exists(ROOT.DS.'app'.DS.'controller'.DS.$className.'.php')){
        require_once(ROOT.DS.'app'.DS.'controller'.DS.$className.'.php');
    }elseif(file_exists(ROOT.DS.'app'.DS.'model'.DS.$className.'.php')){
        require_once(ROOT.DS.'app'.DS.'model'.DS.$className.'.php');
    }
}


Router::route($url);
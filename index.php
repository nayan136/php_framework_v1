<?php

define('DS',DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

//$url = isset($_SERVER['PATH_INFO'])? explode('/',trim($_SERVER['PATH_INFO'],'/')):['/'];
/*
 * eg: admin/uesr as string
 */
$url = isset($_SERVER['PATH_INFO'])?trim($_SERVER['PATH_INFO'],'/'):'/';

require_once(ROOT.DS.'config'.DS.'autoload.php');
<?php

define('DS',DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('BASE_URL',ltrim($_SERVER['REQUEST_URI'],'/'));

require_once(ROOT.DS.'config'.DS.'autoload.php');

$app = new App();
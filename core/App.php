<?php
class App{

    private $_url = "";

    public function __construct()
    {
        $this->parseUrl();
        Router::route($this->_url);
    }

    private function parseUrl(){
        $this->_url = isset($_SERVER['PATH_INFO'])?trim($_SERVER['PATH_INFO'],'/'):'/';
    }

}
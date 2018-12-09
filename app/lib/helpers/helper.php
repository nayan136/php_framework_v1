<?php

function dnd($array){
    if(APP_DEBUG){
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

}

function toArray($data){

    if(!is_array($data)){
        return (array)$data;
    }
    return $data;
}

function printError($string){
    if(APP_DEBUG){
        echo $string;
    }
}

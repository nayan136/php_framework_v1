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
        echo $string."<br>";
    }
}


function url($link){
    trim($link,'/');
    echo $link;
}

function insertIntoArray($position,$originalArray, $insertArray){
    $position--;
    $newArray = array();
    $insertArray = toArray($insertArray);
    dnd($insertArray);
    // add at last position in array
    if(sizeof($originalArray) == $position){
        $newArray = array_merge($originalArray,$insertArray);
    }elseif ($position < sizeof($originalArray) && $position > 0){
        $lengthOfInsertArray = sizeof($insertArray);
        $newArray = array_merge(array_slice($originalArray,0,$position)+$insertArray);
        $newArray = array_merge($newArray,array_slice($originalArray,$position,$lengthOfInsertArray));
    }

    dnd($newArray);
    printError(sizeof($originalArray));
    printError($position);
//    $lengthOfInsertArray = sizeof($insertArray);
//    printError($position);
//    dnd(array_slice($originalArray,0,$position));
//    $arr = array_slice($originalArray,0,$position)+$insertArray+array_slice($originalArray,$position,$lengthOfInsertArray);
    return $newArray;
}

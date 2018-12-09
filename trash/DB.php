<?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'restaurant';

    $conn = mysqli_connect($host,$username,$password,$database);
    if(mysqli_connect_errno()){
        die("Connection Error");
    }
//    echo "Connect Successfully";


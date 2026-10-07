<?php
//initializes DB connection
$host = "localhost";
$user = "root";                 //DB username
$password = '';                 //DB Password
$database = "school";           //DB name

$conn = new mysqli($host, $user, $password, $database);//create connection via mysqli, PLEASE DO NOT FORGET THE ARRANGEMENTS JUSQQ

//check if connection success/failed
if($conn->connect_error){
    die("connection failed".$conn->connect_error);
}



?>
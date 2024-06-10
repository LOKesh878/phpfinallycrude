<?php

$servername="127.0.0.1:3307";
$username="root";
$password="";
$dbname="lokeshdb";


$conn=mysqli_connect($servername,$username,$password,$dbname);
if(!$conn){
    echo "connection failed".mysqli_connect_error();
}


?>
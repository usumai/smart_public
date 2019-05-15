<?php

ini_set('max_execution_time', 0);

$smartm_software_version = "1";
$smartm_db_version = "1";



$hostname = "localhost";
$username = "root";
$dbname   = "test"; 
$password = "";




$servername = "";
// Create connection
// $con = new mysqli($servername, $username, $password, $dbname);
$con = new mysqli($servername, $username, $password);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} 


// Test if the device is connected to the internet
function is_connected()
{
    $connected = @fsockopen("www.example.com", 80);//website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
        $is_conn = "connected";
    }else{
        $is_conn = false; //action in connection failure
        $is_conn = "not connected";
    }
    return $is_conn;
}
$is_conn = is_connected();
// echo $is_conn;

?>
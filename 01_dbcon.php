<?php

ini_set('max_execution_time', 0);




$hostname = "localhost";
$username = "root";
$password = "";


// max_execution_time=3000000
// memory_limit=128000M
// post_max_size=100000M
// upload_max_filesize=200000M


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


$sql = "SELECT count(*) as dbexists FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'smartdb'";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dbexists    = $row["dbexists"];
}}
// echo "<br><br><br>".$dbexists; 
if ($dbexists==0) {
    header("Location: 05_action.php?act=sys_initialise");
}



?>
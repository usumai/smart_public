<?php


$servername = "";
$username   = "root";
$password   = "";
echo "<pre>Establishing database connection</pre>";
$con = new mysqli($servername, $username, $password);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} 
echo "<pre>Dropping database</pre>";
$sql_save = "DROP DATABASE smartdb;";
mysqli_multi_query($con,$sql_save); 

$addr_git= ' "\Program Files\Git\bin\git"  ';
$output  = shell_exec($addr_git.' init 2>&1'); 
$output .= shell_exec($addr_git.' clean  -d  -f .');
$output .= shell_exec($addr_git.' reset --hard');  
$output .= shell_exec($addr_git.' pull https://github.com/usumai/110_smart.git');
echo "<pre>$output</pre>";


header("Location: index.php");

?>
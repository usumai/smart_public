<?php
$act = $_GET["act"];
$addr_git = ' "\Program Files\Git\bin\git"  ';

//CRUD
if ($act=='sys_pull_master') {
	//This file updates the local software with the currently published software

	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output .= shell_exec($addr_git.' clean  -d  -f .');
	$output .= shell_exec($addr_git.' reset --hard');  
	$output .= shell_exec($addr_git.' pull https://github.com/usumai/smart_public.git');
	echo "<pre>$output</pre>";

	header("Location: index.php");

}elseif ($act=='sys_push_development') {//Typically don't use this. Developer only. User access will allow everything to be fucked up.
	// if(function_exists('shell_exec')) {
	//     echo "exec is enabled";
	// }
	ini_set('max_execution_time', 0);
	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output .= shell_exec($addr_git.' add -A'); 
	$output .= shell_exec($addr_git.' commit -m "auto commit"'); 
	$output .= shell_exec($addr_git.' remote add origin https://github.com/usumai/smart_public.git'); 
	$output .= shell_exec($addr_git.' push -u origin master');
	// $output = shell_exec('set 2>&1');  The 2>&1 makes the command get all errors
	echo "<pre>$output</pre>";
	
	header("Location: index.php");
}



?>
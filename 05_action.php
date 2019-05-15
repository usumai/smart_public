<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["actionType"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}
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

}elseif ($act=='sys_push_master') {//Typically don't use this. Developer only. User access will allow everything to be fucked up.
	// if(function_exists('shell_exec')) {
	//     echo "exec is enabled";
	// }
	ini_set('max_execution_time', 0);
	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output  = shell_exec($addr_git.' checkout working_development'); 
	$output .= shell_exec($addr_git.' add -A'); 
	$output .= shell_exec($addr_git.' commit -m "auto commit"'); 
	$output .= shell_exec($addr_git.' remote add origin https://github.com/usumai/smart_public.git'); 
	$output .= shell_exec($addr_git.' push -u origin working_development');
	// $output = shell_exec('set 2>&1');  The 2>&1 makes the command get all errors
	echo "<pre>$output</pre>";
	
	header("Location: index.php");

}elseif ($act=='sys_initialise') {

	$sql = "SELECT count(*) as dbexists FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'smartdb'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$dbexists    = $row["dbexists"];
	}}
echo $dbexists;
// 	$sql_save = "CREATE TABLE ".$dbname.".smart_l01_settings (`smartm_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`active_profile_id` INT NULL DEFAULT NULL,`last_access_date` DATETIME NULL,`last_access_profile_id` INT(11) NULL,`smartm_software_version` INT(11) NULL,`smartm_db_version` INT(11) NULL,`rr_extract_date` DATETIME NULL, `rr_extract_user` VARCHAR(255) NULL DEFAULT NULL,`journal_id` INT(11) NULL,`help_shown` INT(11) NULL,`theme_type` INT(11) NULL, PRIMARY KEY (`smartm_id`),UNIQUE INDEX `smartm_id_UNIQUE` (`smartm_id` ASC));";
// 	// echo "<br><br>".$sql_save;
// 	mysqli_multi_query($con,$sql_save);
// create database smart;
}



?>
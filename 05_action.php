<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["actionType"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}
$addr_git = ' "\Program Files\Git\bin\git"  ';

$log = "<br>"."Initialising action file";
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
	$output .= shell_exec($addr_git.' add -A'); 
	$output .= shell_exec($addr_git.' commit -m "auto commit"'); 
	$output .= shell_exec($addr_git.' remote add origin https://github.com/usumai/smart_public.git'); 
	$output .= shell_exec($addr_git.' push -u origin master');
	// $output = shell_exec('set 2>&1');  The 2>&1 makes the command get all errors
	echo "<pre>$output</pre>";
	
	header("Location: index.php");

}elseif ($act=='sys_initialise') {
    $dbname = "smartdb";

// Delete after testing
$sql_save = "DROP DATABASE $dbname;";
mysqli_multi_query($con,$sql_save); 



	$sql = "SELECT count(*) as dbexists FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$dbexists    = $row["dbexists"];
	}}
    $log .= "<br>"."search for database $dbname yeailded $dbexists results";
    if ($dbexists==0) {
        $log .= "<br>"."creating database: $dbname";
        $sql_save = "CREATE DATABASE $dbname;";
        mysqli_multi_query($con,$sql_save);    

 
        $log .= "<br>"."creating $dbname.sm_11_assets ";
        $sql_save = "
                    CREATE TABLE `$dbname`.`sm_11_assets` (
                    `ass_id` int(11) NOT NULL AUTO_INCREMENT,
                    `create_date` datetime DEFAULT NULL,
                    `create_user` varchar(255) DEFAULT NULL,
                    `delete_date` datetime DEFAULT NULL,
                    `delete_user` varchar(255) DEFAULT NULL,
                    `stkm_id` int(11) DEFAULT NULL,
                    `storage_id` int(11) DEFAULT NULL,
                    `stk_include` int(11) DEFAULT NULL,

                    `Asset` varchar(255) DEFAULT NULL,
                    `Subnumber` varchar(255) DEFAULT NULL,
                    `impairment_code` varchar(255) DEFAULT NULL,

                    `genesis_cat` varchar(255) DEFAULT NULL,
                    `first_found_flag` int(11) NOT NULL,
                    `rr_id` int(11) DEFAULT NULL,
                    `fingerprint` varchar(255) DEFAULT NULL,


                    `res_create_date` datetime DEFAULT NULL,
                    `res_create_user` varchar(255) DEFAULT NULL,
                    `res_reason_code` varchar(255) DEFAULT NULL,
                    `res_reason_code_desc` varchar(255) DEFAULT NULL,
                    `res_impairment_completed` int(1) DEFAULT NULL,
                    `res_completed` int(1) DEFAULT NULL,
                    `res_comment` varchar(255) DEFAULT NULL,

                    `AssetDesc1` varchar(255) DEFAULT NULL,
                    `AssetDesc2` varchar(255) DEFAULT NULL,
                    `AssetMainNoText` varchar(255) DEFAULT NULL,
                    `Class` varchar(255) DEFAULT NULL,
                    `classDesc` varchar(255) DEFAULT NULL,
                    `assetType` varchar(255) DEFAULT NULL,
                    `Inventory` varchar(255) DEFAULT NULL,
                    `Quantity` int(11) DEFAULT NULL,
                    `SNo` varchar(255) DEFAULT NULL,
                    `InventNo` varchar(255) DEFAULT NULL,
                    `accNo` varchar(255) DEFAULT NULL,
                    `Location` varchar(255) DEFAULT NULL,
                    `Room` varchar(255) DEFAULT NULL,
                    `State` varchar(255) DEFAULT NULL,
                    `latitude` varchar(255) DEFAULT NULL,
                    `longitude` varchar(255) DEFAULT NULL,
                    `CurrentNBV` decimal(15,2) DEFAULT NULL,
                    `AcqValue` decimal(15,2) DEFAULT NULL,
                    `OrigValue` decimal(15,2) DEFAULT NULL,
                    `ScrapVal` decimal(15,2) DEFAULT NULL,
                    `ValMethod` varchar(255) DEFAULT NULL,
                    `RevOdep` varchar(255) DEFAULT NULL,
                    `CapDate` datetime DEFAULT NULL,
                    `LastInv` datetime DEFAULT NULL,
                    `DeactDate` datetime DEFAULT NULL,
                    `PlRetDate` datetime DEFAULT NULL,
                    `CCC_ParentName` varchar(255) DEFAULT NULL,
                    `CCC_GrandparentName` varchar(255) DEFAULT NULL,
                    `GrpCustod` varchar(255) DEFAULT NULL,
                    `CostCtr` varchar(255) DEFAULT NULL,
                    `WBSElem` varchar(255) DEFAULT NULL,
                    `Fund` varchar(255) DEFAULT NULL,
                    `RspCCtr` varchar(255) DEFAULT NULL,
                    `CoCd` varchar(255) DEFAULT NULL,
                    `PlateNo` varchar(255) DEFAULT NULL,
                    `Vendor` varchar(255) DEFAULT NULL,
                    `Mfr` varchar(255) DEFAULT NULL,
                    `UseNo` varchar(255) DEFAULT NULL,


                    `res_AssetDesc1` varchar(255) DEFAULT NULL,
                    `res_AssetDesc2` varchar(255) DEFAULT NULL,
                    `res_AssetMainNoText` varchar(255) DEFAULT NULL,
                    `res_Class` varchar(255) DEFAULT NULL,
                    `res_classDesc` varchar(255) DEFAULT NULL,
                    `res_assetType` varchar(255) DEFAULT NULL,
                    `res_Inventory` varchar(255) DEFAULT NULL,
                    `res_Quantity` int(11) DEFAULT NULL,
                    `res_SNo` varchar(255) DEFAULT NULL,
                    `res_InventNo` varchar(255) DEFAULT NULL,
                    `res_accNo` varchar(255) DEFAULT NULL,
                    `res_Location` varchar(255) DEFAULT NULL,
                    `res_Room` varchar(255) DEFAULT NULL,
                    `res_State` varchar(255) DEFAULT NULL,
                    `res_latitude` varchar(255) DEFAULT NULL,
                    `res_longitude` varchar(255) DEFAULT NULL,
                    `res_CurrentNBV` decimal(15,2) DEFAULT NULL,
                    `res_AcqValue` decimal(15,2) DEFAULT NULL,
                    `res_OrigValue` decimal(15,2) DEFAULT NULL,
                    `res_ScrapVal` decimal(15,2) DEFAULT NULL,
                    `res_ValMethod` varchar(255) DEFAULT NULL,
                    `res_RevOdep` varchar(255) DEFAULT NULL,
                    `res_CapDate` datetime DEFAULT NULL,
                    `res_LastInv` datetime DEFAULT NULL,
                    `res_DeactDate` datetime DEFAULT NULL,
                    `res_PlRetDate` datetime DEFAULT NULL,
                    `res_CCC_ParentName` varchar(255) DEFAULT NULL,
                    `res_CCC_GrandparentName` varchar(255) DEFAULT NULL,
                    `res_GrpCustod` varchar(255) DEFAULT NULL,
                    `res_CostCtr` varchar(255) DEFAULT NULL,
                    `res_WBSElem` varchar(255) DEFAULT NULL,
                    `res_Fund` varchar(255) DEFAULT NULL,
                    `res_RspCCtr` varchar(255) DEFAULT NULL,
                    `res_CoCd` varchar(255) DEFAULT NULL,
                    `res_PlateNo` varchar(255) DEFAULT NULL,
                    `res_Vendor` varchar(255) DEFAULT NULL,
                    `res_Mfr` varchar(255) DEFAULT NULL,
                    `res_UseNo` varchar(255) DEFAULT NULL,

                    `res_isq_5` varchar(255) DEFAULT NULL,
                    `res_isq_6` varchar(255) DEFAULT NULL,
                    `res_isq_7` varchar(255) DEFAULT NULL,
                    `res_isq_8` varchar(255) DEFAULT NULL,
                    `res_isq_9` varchar(255) DEFAULT NULL,
                    `res_isq_10` varchar(255) DEFAULT NULL,
                    `res_isq_13` varchar(255) DEFAULT NULL,
                    `res_isq_14` varchar(255) DEFAULT NULL,
                    `res_isq_15` datetime DEFAULT NULL,
                    PRIMARY KEY (`ass_id`),
                    UNIQUE KEY `ass_id_UNIQUE` (`ass_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        mysqli_multi_query($con,$sql_save);   




        
           
    }

}
echo $log;


?>
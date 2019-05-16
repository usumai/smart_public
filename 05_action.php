<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["act"])) {
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

          $sql_save = "CREATE TABLE $dbname.sm10_set (`smartm_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`active_profile_id` INT NULL DEFAULT NULL,`last_access_date` DATETIME NULL,`last_access_profile_id` INT(11) NULL,`smartm_software_version` INT(11) NULL,`smartm_db_version` INT(11) NULL,`rr_extract_date` DATETIME NULL, `rr_extract_user` VARCHAR(255) NULL DEFAULT NULL,`journal_id` INT(11) NULL,`help_shown` INT(11) NULL,`theme_type` INT(11) NULL, PRIMARY KEY (`smartm_id`),UNIQUE INDEX `smartm_id_UNIQUE` (`smartm_id` ASC));";
          mysqli_multi_query($con,$sql_save);

         $sql_save = "INSERT INTO $dbname.sm10_set (create_date, update_date, last_access_date, journal_id, help_shown, theme_type) VALUES (NOW(), NOW(), NOW(),1,0,0); ";
          mysqli_multi_query($con,$sql_save);

          $sql_save = "CREATE TABLE $dbname.sm11_pro (`profile_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`profile_name` VARCHAR(255) NULL DEFAULT NULL,`profile_drn` VARCHAR(255) NULL DEFAULT NULL,`profile_phone_number` VARCHAR(255) NULL DEFAULT NULL,`profile_pic` LONGTEXT NULL DEFAULT NULL,`profile_color_a` VARCHAR(255) NULL DEFAULT NULL,`profile_color_b` VARCHAR(255) NULL DEFAULT NULL,PRIMARY KEY (`profile_id`),UNIQUE INDEX `profile_id_UNIQUE` (`profile_id` ASC));";
          mysqli_multi_query($con,$sql_save);

          $sql_save = "CREATE TABLE $dbname.sm12_rwr (`rr_id` INT(11) NOT NULL AUTO_INCREMENT,`Asset` VARCHAR(15) NULL DEFAULT NULL,`accNo` VARCHAR(5) NULL DEFAULT NULL, `InventNo` VARCHAR(30) NULL DEFAULT NULL, `AssetDesc1` VARCHAR(255) NULL DEFAULT NULL, `Class` VARCHAR(255) NULL DEFAULT NULL, `ParentName` VARCHAR(255) NULL DEFAULT NULL, PRIMARY KEY (`rr_id`),UNIQUE INDEX `rr_id_UNIQUE` (`rr_id` ASC));";
          mysqli_multi_query($con,$sql_save);

          $sql_save = "CREATE TABLE $dbname.sm13_stk (`stkm_id` INT NOT NULL AUTO_INCREMENT,`stk_id` INT NULL,`stk_name` VARCHAR(255) NULL,`extract_date` DATETIME NULL,`extract_user` VARCHAR(255) NULL,`delete_date` DATETIME NULL,`delete_user` VARCHAR(255) NULL,`stk_include` INT NULL,`journal_text` LONGTEXT NULL,PRIMARY KEY (`stkm_id`),UNIQUE INDEX `stkm_id_UNIQUE` (`stkm_id` ASC));";
          mysqli_multi_query($con,$sql_save);
 
          $log .= "<br>"."creating $dbname.sm14_ass ";
          $sql_save = "
                    CREATE TABLE `$dbname`.`sm14_ass` (
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


        
           
    }//End create database area

     header("Location: index.php");



}elseif ($act=='save_stk_toggle') {
     $stkm_id = $_GET["stkm_id"];
     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id = ".$stkm_id.";";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $stkm_id       = $row["stkm_id"];
               $stk_include   = $row["stk_include"];
     }}
     if ($stk_include==1) {
          $sql_save = "UPDATE smartdb.sm13_stk SET stk_include=0 WHERE stkm_id = $stkm_id;";
     }else{
          $sql_save = "UPDATE smartdb.sm13_stk SET stk_include=1 WHERE stkm_id = $stkm_id;";
     }
     mysqli_multi_query($con,$sql_save);

     header("Location: index.php");

}elseif ($act=='upload_file') {
     $dev=false;
     // print_r($_FILES); 
     $target_file = $_FILES["fileToUpload"]["tmp_name"];
     // $target_file = $_FILES["fileToUpload"]["name"];
     $fileContents = file_get_contents($target_file);
     // echo $fileContents;
     // $fileContents = utf8_encode($fileContents);

     //This is to remove the unicode encoding on the file. It leaves two characters at the start of the file which throw an error.
     $fileContents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $fileContents);

          // if ($dev) { echo "<br>fileContents:".$fileContents; }


     // $fileContents = "{"import": {"type":"stocktake","extract_date":"2018-09-12","extract_user":"lucas.taulealeausuma", "stkID": 1054,"stkName":"40072_0836_Larrakeyah_Barracks_(incorporates_HMAS_Coonawarra)_LBI"}}";
     // $fileContents = file_get_contents("test.txt");
     // echo $fileContents;
     $sampleArr     = json_decode($fileContents, true);
     $importType    = $sampleArr['import']['type'];
     if ($dev) { echo "<br>Import type: ".$importType; }
     // echo "<br>Import type: ".$importType;
     // echo "<br>extractDate: ".$extract_date;
     // echo "<br>extract_user: ".$extract_user;


     $extract_date                 = $sampleArr['import']['extract_date'];
     $extract_user                 = $sampleArr['import']['extract_user'];
     $stkm_id_old                  = $sampleArr['import']['stkm_id'];
     $stk_id                       = $sampleArr['import']['stk_id'];
     $stk_name                     = $sampleArr['import']['stk_name'];
     $active_profile_id            = $sampleArr['import']['active_profile_id'];
     $smartm_software_version      = $sampleArr['import']['smartm_software_version'];
     $smartm_db_version            = $sampleArr['import']['smartm_db_version'];
     $rr_extract_date              = $sampleArr['import']['rr_extract_date'];
     $journal_text                 = $sampleArr['import']['journal_text'];
     $export_date                  = $sampleArr['import']['export_date'];
     $count_total                  = $sampleArr['import']['count_total'];
     $count_original               = $sampleArr['import']['count_original'];
     $count_firstfound             = $sampleArr['import']['count_firstfound'];
     $count_completed              = $sampleArr['import']['count_completed'];
     $results                      = $sampleArr['import']['results'];

     if ($dev) {
          echo "<br>extract_date:".$extract_date 
               ."<br>extract_user:".$extract_user 
               ."<br>stkm_id_old:".$stkm_id_old 
               ."<br>stk_id:".$stk_id 
               ."<br>stk_name:".$stk_name 
               ."<br>active_profile_id:".$active_profile_id 
               ."<br>smartm_software_version:".$smartm_software_version
               ."<br>smartm_db_version:".$smartm_db_version
               ."<br>rr_extract_date:".$rr_extract_date
               ."<br>journal_text:".$journal_text
               ."<br>export_date:".$export_date 
               ."<br>count_total:".$count_total 
               ."<br>export_date:".$export_date;
               // print_r($results) ;
     }


     $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,extract_date,extract_user) VALUES ('".$stk_id."','".$stk_name."','".$extract_date."','".$extract_user."'); ";
     if ($dev) { echo "<br>sql_save: ".$sql_save; }
     mysqli_multi_query($con,$sql_save);

     $sql = "SELECT * FROM smartdb.sm13_stk ORDER BY stkm_id DESC LIMIT 1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $stkm_id_new    = $row["stkm_id"];
     }}
     foreach($results as $asset) {
               $ass_id                       = $asset['ass_id'];
               $delete_date_ass              = $asset['delete_date_ass'];
               $delete_user_ass              = $asset['delete_user_ass'];
               $delete_date_stk              = $asset['delete_date_stk'];
               $storage_id                   = $asset['storage_id'];
               $Asset                        = $asset['Asset'];
               $first_found_flag             = $asset['first_found_flag'];
               $impairment_code              = $asset['impairment_code'];

               $AssetDesc1                   = $asset['AssetDesc1'];
               $AssetDesc2                   = $asset['AssetDesc2'];
               $AssetMainNoText              = $asset['AssetMainNoText'];
               $Class                        = $asset['Class'];
               $Inventory                    = $asset['Inventory'];
               $Quantity                     = $asset['Quantity'];
               $SNo                          = $asset['SNo'];
               $InventNo                     = $asset['InventNo'];
               $accNo                        = $asset['accNo'];
               $site_name                    = $asset['site_name'];
               $Location                     = $asset['Location'];
               $Room                         = $asset['Room'];
               $State                        = $asset['State'];
               $latitude                     = $asset['latitude'];
               $longitude                    = $asset['longitude'];
               $CurrentNBV                   = $asset['CurrentNBV'];
               $AcqValue                     = $asset['AcqValue'];
               $OrigValue                    = $asset['OrigValue'];
               $ScrapVal                     = $asset['ScrapVal'];
               $ValMethod                    = $asset['ValMethod'];
               $RevOdep                      = $asset['RevOdep'];
               $CapDate                      = $asset['CapDate'];
               $LastInv                      = $asset['LastInv'];
               $DeactDate                    = $asset['DeactDate'];
               $PlRetDate                    = $asset['PlRetDate'];
               $CCC_ParentName               = $asset['CCC_ParentName'];
               $CCC_GrandparentName          = $asset['CCC_GrandparentName'];
               $GrpCustod                    = $asset['GrpCustod'];
               $CostCtr                      = $asset['CostCtr'];
               $WBSElem                      = $asset['WBSElem'];
               $Fund                         = $asset['Fund'];
               $RspCCtr                      = $asset['RspCCtr'];
               $CoCd                         = $asset['CoCd'];
               $PlateNo                      = $asset['PlateNo'];
               $Vendor                       = $asset['Vendor'];
               $Mfr                          = $asset['Mfr'];
               $UseNo                        = $asset['UseNo'];

               $sql_save_assets=" INSERT INTO smartdb.sm14_ass (create_date, stkm_id, storage_id, Asset, AssetDesc1, AssetDesc2, AssetMainNoText, Class, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo) VALUES(NOW(), '".$stkm_id_new."','".$storage_id."','".$Asset."','".$AssetDesc1."','".$AssetDesc2."','".$AssetMainNoText."','".$Class."','".$Inventory."','".$Quantity."','".$SNo."','".$InventNo."','".$accNo."','".$Location."','".$Room."','".$State."','".$latitude."','".$longitude."','".$CurrentNBV."','".$AcqValue."','".$OrigValue."','".$ScrapVal."','".$ValMethod."','".$RevOdep."','".$CapDate."','".$LastInv."','".$DeactDate."','".$PlRetDate."','".$CCC_ParentName."','".$CCC_GrandparentName."','".$GrpCustod."','".$CostCtr."','".$WBSElem."','".$Fund."','".$RspCCtr."','".$CoCd."','".$PlateNo."','".$Vendor."','".$Mfr."','".$UseNo."'); ";
               mysqli_multi_query($con,$sql_save_assets);
               // if ($dev) {echo "<br>".$Asset;}
               if ($dev) {echo "<br><br>".$sql_save_assets;}

          }
     header("Location: index.php");

}
echo $log;


?>
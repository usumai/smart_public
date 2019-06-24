<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}
$addr_git = ' "\Program Files\Git\bin\git"  ';
$log = "<br>"."Initialising action file";
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//CRUD
if ($act=='sys_pull_master') {
	//This file updates the local software with the currently published software

	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output .= shell_exec($addr_git.' clean  -d  -f .');
	$output .= shell_exec($addr_git.' reset --hard');  
	$output .= shell_exec($addr_git.' pull https://github.com/usumai/smart_public.git');
	echo "<pre>$output</pre>";

	header("Location: index.php");

}elseif ($act=='sys_open_image_folder') {
    // $output  = shell_exec('cd/'); 
    shell_exec('cd C:\xampp\htdocs\110_smart\images'); 
    // shell_exec('cd/ C:\users\Google Drive\015_www\110_smarter_master\images\ ');
    // $output  = shell_exec('cd images '); 
    shell_exec('start .'); 
    header("Location: index.php");


}elseif ($act=='sys_push_master') {//Typically don't use this. Developer only. User access will allow everything to be fucked up.
	// if(function_exists('shell_exec')) {
	//     echo "exec is enabled";
	// }
	ini_set('max_execution_time', 0);
	$output  = shell_exec($addr_git.' init 2>&1'); // The 2>&1 makes the command get all errors
	$output .= shell_exec($addr_git.' add -A'); 
	$output .= shell_exec($addr_git.' commit -m "auto commit"'); 
	$output .= shell_exec($addr_git.' remote add origin https://github.com/usumai/smart_public.git'); 
	$output .= shell_exec($addr_git.' push -u origin master');
	
	echo "<pre>$output</pre>";
	
	header("Location: index.php");


}elseif ($act=='sys_check_for_updates') {
     //This file updates the local software with the currently published software

     header("Location: index.php");

}elseif ($act=='sys_initialise') {
     $dbname = "smartdb";

   $log .= "<br>"."creating database: $dbname";
   $sql_save = "CREATE DATABASE $dbname;";
   mysqli_multi_query($con,$sql_save); 

     header("Location: 05_action.php?act=sys_reset_data");




}elseif ($act=='sys_reset_data') {
     $dbname = "smartdb";

     $sql_save = "DROP DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 

   $log .= "<br>"."creating database: $dbname";
   $sql_save = "CREATE DATABASE $dbname;";
   mysqli_multi_query($con,$sql_save); 

     $sql_save = "CREATE TABLE $dbname.sm10_set (`smartm_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`active_profile_id` INT NULL DEFAULT NULL,`last_access_date` DATETIME NULL,`last_access_profile_id` INT(11) NULL,`smartm_software_version` INT(11) NULL,`smartm_db_version` INT(11) NULL,`rr_extract_date` DATETIME NULL, `rr_extract_user` VARCHAR(255) NULL DEFAULT NULL,`journal_id` INT(11) NULL,`help_shown` INT(11) NULL,`theme_type` INT(11) NULL,`date_last_update_check` DATETIME NULL, PRIMARY KEY (`smartm_id`),UNIQUE INDEX `smartm_id_UNIQUE` (`smartm_id` ASC));";
     mysqli_multi_query($con,$sql_save);

    $sql_save = "INSERT INTO $dbname.sm10_set (create_date, update_date, last_access_date, journal_id, help_shown, theme_type, smartm_software_version) VALUES (NOW(), NOW(), NOW(),1,0,0,4); ";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm11_pro (`profile_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`profile_name` VARCHAR(255) NULL DEFAULT NULL,`profile_drn` VARCHAR(255) NULL DEFAULT NULL,`profile_phone_number` VARCHAR(255) NULL DEFAULT NULL,`profile_pic` LONGTEXT NULL DEFAULT NULL,`profile_color_a` VARCHAR(255) NULL DEFAULT NULL,`profile_color_b` VARCHAR(255) NULL DEFAULT NULL,PRIMARY KEY (`profile_id`),UNIQUE INDEX `profile_id_UNIQUE` (`profile_id` ASC));";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm12_rwr (`rr_id` INT(11) NOT NULL AUTO_INCREMENT,`Asset` VARCHAR(15) NULL DEFAULT NULL,`accNo` VARCHAR(5) NULL DEFAULT NULL, `InventNo` VARCHAR(30) NULL DEFAULT NULL, `AssetDesc1` VARCHAR(255) NULL DEFAULT NULL, `Class` VARCHAR(255) NULL DEFAULT NULL, `ParentName` VARCHAR(255) NULL DEFAULT NULL, `rr_included` int(11) DEFAULT NULL, PRIMARY KEY (`rr_id`),UNIQUE INDEX `rr_id_UNIQUE` (`rr_id` ASC));";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm13_stk (`stkm_id` INT NOT NULL AUTO_INCREMENT,`stk_id` INT NULL,`stk_name` VARCHAR(255) NULL,`dpn_extract_date` DATETIME NULL,`dpn_extract_user` VARCHAR(255) NULL,`smm_extract_date` DATETIME NULL,`smm_extract_user` VARCHAR(255) NULL,`smm_delete_date` DATETIME NULL,`smm_delete_user` VARCHAR(255) NULL,`stk_include` INT NULL,`rowcount_original` INT NULL,`journal_text` LONGTEXT NULL,PRIMARY KEY (`stkm_id`),UNIQUE INDEX `stkm_id_UNIQUE` (`stkm_id` ASC));";
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
               `first_found_flag` int(11) DEFAULT NULL,
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


     $sql_save = "CREATE TABLE $dbname.sm15_rc (
                    `reason_code_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `res_reason_code` VARCHAR(255) NULL, 
                    `rc_desc` VARCHAR(255) NULL, 
                    `rc_long_desc` VARCHAR(255) NULL, 
                    `rc_examples` VARCHAR(255) NULL, 
                    `rc_action` VARCHAR(255) NULL,
                    `rc_section` VARCHAR(255) NULL,
                    PRIMARY KEY (`reason_code_id`),
                    UNIQUE INDEX `reason_code_id_UNIQUE` (`reason_code_id` ASC));";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);

     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES  ('ND10','No financial discrepancies','Asset Found - No Action required','Asset found with all details correct.','ND','ND'); "; 
     echo $sql_save;
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES  ('NC10','Not In Count','Assets excluded from count.','Asset where the site is inaccessible, i.e. remote locality or project construction areas.','NIC','ERR'); ";
     mysqli_multi_query($con,$sql_save); 
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('AF10','Asset Found - Ownership','Asset ownership error. The asset management system to be updated to reflect correct owners.','Asset found with incorrect Cost Centre Code.','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('AF15','Asset Found - Incorrect Register','Asset found - asset accounted for in the incorrect asset register/system.','An asset found that should be accounted for in MILIS and not ROMAN.','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('AF20','Asset Found - Location Transfers','Asset found, however, asset register indicates the asset resides in another base/site.','Demountable moved between Defence properties without asset transfer documentation sent to DFG.','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('FF10','Asset First Found - Project Acquisition','Asset first found - Procured under a National Project Works.','Project asset not brought on to the asset register/system, not communicated to be added to the asset register/system.','SAV','FF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('FF15','Asset First Found - Local Estate Works','Asset first found - Procured under Local Estate Works','Asset acquired under the local estate works contract (repair/replacement). Procurement not communicated to DFG, and not added to the asset register/system.','SAV','FF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('FF20','Asset First Found - Externally Acquired','Asset first found - asset received from organisation external to Defence.','Asset acquired from another government department without documentation. Asset could have been `Gifted to Defence`.','SAV','FF'); ";
     mysqli_multi_query($con,$sql_save); 
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('FF25','Asset First Found - Unexplained','Asset first found - Unexplained.','Asset purchase with no history, no explanation as to its existence. Not communicated to DFG, and not added to the FAR','SAV','FF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('NF10','Asset Not Found - Project Disposal','Asset not found - Disposal under National Project','Asset disposed under a National Project not communicated to DFG, not removed from the asset register/system.','SAV','NF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('NF15','Asset Not Found - Local Disposal','Asset not found - Locally disposed asset.','Asset disposal, failed to advise DFG of disposal, not removed from the asset register/system.','SAV','NF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('NF20','Asset Not Found - Trade in','Asset not found - Procurement Trade-In','Asset used as `Traded-in` in the procurement process, asset owner failed to follow correct disposal process, not communicated to DFG, not removed from the asset register/system.','SAV','NF'); ";
     mysqli_multi_query($con,$sql_save); 
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('NF25','Asset Not Found - Local Estate Works','Asset not found - Disposal under Local Estate Works','Asset disposed under a local works, not communicated to DFG, not removed from the asset register/system.','SAV','NF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('NF30','Asset Not Found - Unexplained','Asset not found - Unexplained','Asset owner cannot provide information as to its whereabouts.','SAV','NF'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('PE10','Prior Stocktake Error','Stocktake Adjustment error in the asset register/ system, where the error has occurred as a direct result of a previous or current stocktake adjustment.','Reversal of a `write-on` action from a previous stocktake. AFF that should not have been created.','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('RE10','Asset Duplication - Different Register','Errors found for the same asset record in separate registers/ systems/company codes where the error is a direct result of register actions by DFG Register Authority.','Duplication: assets recorded and financially accounted for in multiple register/ systems (ROMAN and MILIS), or in multiple Company Codes, (1000 and 4100).','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('RE15','Asset Duplication - Same Register','Errors found for the same asset record in same asset register/ system, where the error is a direct result of register actions by the Register Authority','Duplication: assets recorded twice for the same physical asset. Assets created as a result of revaluation adjustments.','SAV','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('FF99','DFG excluded adjustments, as approved by DFG.','Assets First Found which are project related, to be removed from the count as approved by DFG.','Pending ROMAN adjustments relating to a Project Rollout. The rollout of these assets will be conducted IAW Project Rollout processes.','NIC','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('RE20','Asset register Error','General non-financial related errors.','Simple record updates such as, location data, barcode updates, transcription, spelling errors, description i.e. asset description not in UPPER CASE.','ND','ERR'); "; 
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('RE25','Asset Split','Errors relating to assets that may form part of Merge/Split process.','A Split error is where a single asset record may have been initially created, however the assets characteristics distinctly display two separate physical assets','SAV','ERR'); ";
     mysqli_multi_query($con,$sql_save); 
     $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES ('RE30','Asset Merge','Errors relating to assets that may form part of Merge/Split process.','A Merge error is where two asset records may have been initially created, when it should have been a single asset record;','SAV','ERR'); "; 
     // echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm16_file (`file_id` INT NOT NULL AUTO_INCREMENT,`file_type` VARCHAR(255) NULL,`file_ref` VARCHAR(255) NULL,`file_desc` VARCHAR(255) NULL,PRIMARY KEY (`file_id`),UNIQUE INDEX `file_id_UNIQUE` (`file_id` ASC));";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm17_history (`history_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL,`create_user` VARCHAR(255) NULL,`history_link` VARCHAR(255) NULL,`history_type` VARCHAR(255) NULL,`history_desc` VARCHAR(255) NULL, PRIMARY KEY (`history_id`));";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);
     $sql_save = "INSERT INTO ".$dbname.".sm17_history (create_date, create_user, history_type, history_desc) VALUES ( NOW(),'System Robot','System Initialisation','The system initiated a new deployment');";
     mysqli_multi_query($con,$sql_save);

     header("Location: index.php");


}else if ($act=="save_invertcolors") {
     $sql = "SELECT * FROM smartdb.sm10_set";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $theme_type    = $row["theme_type"];
     }}

     if ($theme_type==1) {
          $sql_save = "UPDATE smartdb.sm10_set SET theme_type = '0' ";
     }else{
          $sql_save = "UPDATE smartdb.sm10_set SET theme_type = '1' ";
     }

     sleep(1);
     // echo $_SERVER['HTTP_REFERER'];
     mysqli_multi_query($con,$sql_save);
     header("Location: ".$_SERVER['HTTP_REFERER']);



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
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=0 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=0 WHERE stkm_id = $stkm_id;";
     }else{
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=1 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=1 WHERE stkm_id = $stkm_id;";
     }
     echo "<br>".$sql_save_stk;
     echo "<br>".$sql_save_ass;
     mysqli_multi_query($con,$sql_save_stk);
     mysqli_multi_query($con,$sql_save_ass);

     header("Location: index.php");

}elseif ($act=='upload_file') {
     $dev=false;
     $target_file = $_FILES["fileToUpload"]["tmp_name"];
     $fileContents = file_get_contents($target_file);

     //This is to remove the unicode encoding on the file. It leaves two characters at the start of the file which throw an error.
     $fileContents  = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $fileContents);
     $arr_full      = json_decode($fileContents, true);
     $arr           = $arr_full['import'];

     function cleanvalue($fieldvalue) {
          $fieldvalue = str_replace("'", "\'", $fieldvalue);
          $fieldvalue = str_replace('"', '\"', $fieldvalue);
          // $fieldvalue = str_replace("""", "/""", $fieldvalue);
          if ($fieldvalue=="") {
               $fieldvalue="NULL";
          }elseif (empty($fieldvalue)) {
               $fieldvalue="NULL";
          }elseif ($fieldvalue=="NULL") {
               $fieldvalue="NULL";
          }elseif ($fieldvalue=="null") {
               $fieldvalue="NULL";
          }else{
               $fieldvalue="'".$fieldvalue."'";
          }
          return $fieldvalue;
     }

     // echo "<br>Type:" .$arr['type'];
     if ($arr['type']=="stocktake") {
          $stk_id                  = $arr['stk_id'];
          $stk_name                = $arr['stk_name'];
          $dpn_extract_date        = $arr['dpn_extract_date'];
          $dpn_extract_user        = $arr['dpn_extract_user'];
          $smm_extract_date        = cleanvalue($arr['smm_extract_date']);
          $smm_extract_user        = cleanvalue($arr['smm_extract_user']);
          $journal_text            = $arr['journal_text'];
          $rowcount_original       = $arr['rowcount_original'];
          $rowcount_firstfound     = $arr['rowcount_firstfound'];
          $rowcount_other          = $arr['rowcount_other'];
          $rowcount_completed      = $arr['rowcount_completed'];
          $assets                  = $arr['results'];

          if ($dev) {
               echo "<br>stk_id:".$stk_id ."<br>stk_name:".$stk_name ."<br>dpn_extract_date:".$dpn_extract_date ."<br>dpn_extract_user:".$dpn_extract_user ."<br>smm_extract_date:".$smm_extract_date ."<br>smm_extract_user:".$smm_extract_user ."<br>journal_text:".$journal_text."<br>rowcount_original:".$rowcount_original ."<br>rowcount_firstfound:".$rowcount_firstfound."<br>rowcount_other:".$rowcount_other."<br>rowcount_completed:".$rowcount_completed;
                    // print_r($assets) ;
          }

          $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,dpn_extract_date,dpn_extract_user,smm_extract_date,smm_extract_user,rowcount_original,journal_text) VALUES ('".$stk_id."','".$stk_name."','".$dpn_extract_date."','".$dpn_extract_user."',".$smm_extract_date.",".$smm_extract_user.",'".$rowcount_original."','".$journal_text."'); ";
          if ($dev) { echo "<br>sql_save: ".$sql_save; }
          mysqli_multi_query($con,$sql_save);

          $sql = "SELECT * FROM smartdb.sm13_stk ORDER BY stkm_id DESC LIMIT 1;";
          $result = $con->query($sql);
          if ($result->num_rows > 0) {
               while($row = $result->fetch_assoc()) {
                    $stkm_id_new    = $row["stkm_id"];
          }}

          // Get a list of the sql fields- the array fields need to match the db for this system to work
          $keys = array_keys($assets["0"]);
          unset($keys[0]); //Remove ass_id since it is a primary key
          unset($keys[107]);//Remove the last array item which is a 'end' holder
          $tags = implode(', ', $keys);

          if(end($assets)['ass_id']=="END") {//We don't want this to happen for exports from the DPN (Which have this)
               array_pop($assets);// Remove the last asset from the array- it is an 'end' holder
          }




          mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
          foreach($assets as $ass) {
               foreach($ass as $fieldname => $fieldvalue) {
                    $ass[$fieldname] = cleanvalue($ass[$fieldname]);
               }
               $sql_save=" INSERT INTO smartdb.sm14_ass ($tags) VALUES(".$ass['create_date'].",".$ass['create_user'].",".$ass['delete_date'].",".$ass['delete_user'].",".$stkm_id_new.",".$ass['storage_id'].",".$ass['stk_include'].",".$ass['Asset'].",".$ass['Subnumber'].",".$ass['impairment_code'].",".$ass['genesis_cat'].",".$ass['first_found_flag'].",".$ass['rr_id'].",".$ass['fingerprint'].",".$ass['res_create_date'].",".$ass['res_create_user'].",".$ass['res_reason_code'].",".$ass['res_reason_code_desc'].",".$ass['res_impairment_completed'].",".$ass['res_completed'].",".$ass['res_comment'].",".$ass['AssetDesc1'].",".$ass['AssetDesc2'].",".$ass['AssetMainNoText'].",".$ass['Class'].",".$ass['classDesc'].",".$ass['assetType'].",".$ass['Inventory'].",".$ass['Quantity'].",".$ass['SNo'].",".$ass['InventNo'].",".$ass['accNo'].",".$ass['Location'].",".$ass['Room'].",".$ass['State'].",".$ass['latitude'].",".$ass['longitude'].",".$ass['CurrentNBV'].",".$ass['AcqValue'].",".$ass['OrigValue'].",".$ass['ScrapVal'].",".$ass['ValMethod'].",".$ass['RevOdep'].",".$ass['CapDate'].",".$ass['LastInv'].",".$ass['DeactDate'].",".$ass['PlRetDate'].",".$ass['CCC_ParentName'].",".$ass['CCC_GrandparentName'].",".$ass['GrpCustod'].",".$ass['CostCtr'].",".$ass['WBSElem'].",".$ass['Fund'].",".$ass['RspCCtr'].",".$ass['CoCd'].",".$ass['PlateNo'].",".$ass['Vendor'].",".$ass['Mfr'].",".$ass['UseNo'].",".$ass['res_AssetDesc1'].",".$ass['res_AssetDesc2'].",".$ass['res_AssetMainNoText'].",".$ass['res_Class'].",".$ass['res_classDesc'].",".$ass['res_assetType'].",".$ass['res_Inventory'].",".$ass['res_Quantity'].",".$ass['res_SNo'].",".$ass['res_InventNo'].",".$ass['res_accNo'].",".$ass['res_Location'].",".$ass['res_Room'].",".$ass['res_State'].",".$ass['res_latitude'].",".$ass['res_longitude'].",".$ass['res_CurrentNBV'].",".$ass['res_AcqValue'].",".$ass['res_OrigValue'].",".$ass['res_ScrapVal'].",".$ass['res_ValMethod'].",".$ass['res_RevOdep'].",".$ass['res_CapDate'].",".$ass['res_LastInv'].",".$ass['res_DeactDate'].",".$ass['res_PlRetDate'].",".$ass['res_CCC_ParentName'].",".$ass['res_CCC_GrandparentName'].",".$ass['res_GrpCustod'].",".$ass['res_CostCtr'].",".$ass['res_WBSElem'].",".$ass['res_Fund'].",".$ass['res_RspCCtr'].",".$ass['res_CoCd'].",".$ass['res_PlateNo'].",".$ass['res_Vendor'].",".$ass['res_Mfr'].",".$ass['res_UseNo'].",".$ass['res_isq_5'].",".$ass['res_isq_6'].",".$ass['res_isq_7'].",".$ass['res_isq_8'].",".$ass['res_isq_9'].",".$ass['res_isq_10'].",".$ass['res_isq_13'].",".$ass['res_isq_14'].",".$ass['res_isq_15']." ); ";
               // echo "<br><br>".$sql_save;
               mysqli_multi_query($con,$sql_save);
          }
     }elseif ($arr['type']=="raw remainder v2") {
          $extract_date  = $arr['extract_date'];
          $extract_user  = $arr['extract_user'];
          if ($dev) { echo "<br>extract_date:".$extract_date; }
          if ($dev) { echo "<br>extract_user:".$extract_user."<br>"; }
          ini_set('max_execution_time', 30000); //300 seconds = 5 minutes

          $sql_delete = "TRUNCATE TABLE smartdb.smart_l03_rr; ";
          mysqli_multi_query($con,$sql_delete);
          $assetRows     = $arr['assetRows'];
          foreach($assetRows as $assetRow) {
               $Asset              = $assetRow['f1'];
               if ($Asset!="END") {
                    $accNo         = $assetRow['f2'];
                    $InventNo      = $assetRow['f3'];
                    $AssetDesc1    = $assetRow['f4'];  
                    $sql_save = "INSERT INTO smartdb.sm12_rwr (Asset,accNo,InventNo,AssetDesc1) VALUES ('$Asset','$accNo','$InventNo','$AssetDesc1'); ";    
                         mysqli_multi_query($con,$sql_save);
               }
          }
          $sql_save_details = " UPDATE smartdb.sm10_set SET rr_extract_date='$extract_date', rr_extract_user='$extract_user'; ";
          mysqli_multi_query($con,$sql_save_details);

          $sql_save = "TRUNCATE TABLE smartdb.sm16_file; ";
          mysqli_multi_query($con,$sql_save);
          
          $abbrevs       = $arr['abbrevs'];
          //print_r($abbrevs);
          foreach($abbrevs as $abbRow) {
               $file_type     = $abbRow['file_type'];
               $file_ref      = $abbRow['file_ref'];
               $file_desc     = $abbRow['file_desc'];
               // echo "<br>".$file_type."   ".$file_ref."  ".$file_desc;
               $sql_save = "INSERT INTO smartdb.sm16_file (file_type,file_ref,file_desc) VALUES ('".$file_type."','".$file_ref."','".$file_desc."'); ";
               mysqli_multi_query($con,$sql_save);
          }

          // Update the RR with the updated abbreviations
          $sql_save = "UPDATE smartdb.sm12_rwr SET ParentName=(SELECT file_desc FROM smartdb.sm16_file WHERE file_type='abbrev_owner' AND file_ref=SUBSTRING(smartdb.sm12_rwr.Asset,1,1)), Class=(SELECT file_desc FROM smartdb.sm16_file WHERE file_type='abbrev_class' AND file_ref=SUBSTRING(smartdb.sm12_rwr.Asset,2,1)), Asset=SUBSTRING(smartdb.sm12_rwr.Asset,3)";
          mysqli_multi_query($con,$sql_save);


          // $sql_save_history = "INSERT INTO ".$dbname.".smart_l10_history (create_date, create_user, history_type, history_desc, history_link) VALUES ( NOW(),'".$current_user."','Raw remainder file upload','User uploaded raw remainder V2 file','108_rr.php');";

     }



     header("Location: index.php");

}elseif ($act=='get_export_stk'){
     $stkm_id = $_GET["stkm_id"];



     $mydate=getdate(date("U"));
     $month_disp = substr("00".$mydate['mon'], -2);
     $day_disp      = substr("00".$mydate['mday'], -2);
     $hours_disp    = substr("00".$mydate['hours'], -2);
     $minutes_disp  = substr("00".$mydate['minutes'], -2);
     $seconds_disp  = substr("00".$mydate['seconds'], -2);
     $date_disp = $mydate['year'].$month_disp.$day_disp;
     $date_disp = $mydate['year'].$month_disp.$day_disp."_".$hours_disp.$minutes_disp.$seconds_disp;

echo $date_disp;
     $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id AND delete_date IS NULL ;";
     $arr_asset = array();
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($r = $result->fetch_assoc()) {
             $arr_asset[] = $r;
     }}

     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=$stkm_id;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $stk_id               = $row["stk_id"];
             $stk_name             = $row["stk_name"];
             $dpn_extract_date     = $row["dpn_extract_date"];
             $dpn_extract_user     = $row["dpn_extract_user"];
             $journal_text         = $row["journal_text"];
     }}

     $txt_file_link = "SMARTm_".$date_disp."_$stk_name.json";
     $fp = fopen($txt_file_link, 'w');

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $smm_create_user    = $row["active_profile_id"];
     }}
     $smm_create_date = $mydate['year']."-".$month_disp."-".$day_disp;

     $sql = " SELECT 
                    sum(CASE WHEN storage_id IS NOT NULL THEN 1 ELSE 0 END) AS rowcount_original,
                    sum(CASE WHEN first_found_flag = 1 THEN 1 ELSE 0 END) AS rowcount_firstfound,
                    sum(CASE WHEN res_completed = 1 THEN 1 ELSE 0 END) AS rowcount_completed,
                    sum(CASE WHEN rr_id IS NOT NULL THEN 1 ELSE 0 END) AS rowcount_other
               FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL";
     $result2 = $con->query($sql);
     if ($result2->num_rows > 0) {
       while($row2 = $result2->fetch_assoc()) {
           $rowcount_original      = $row2["rowcount_original"];
           $rowcount_firstfound    = $row2["rowcount_firstfound"];
           $rowcount_completed     = $row2["rowcount_completed"];
           $rowcount_other         = $row2["rowcount_other"];
     }}

     $response = array();
     $response['import']['type']                  = "stocktake";
     $response['import']['stkm_id']               = $stkm_id;
     $response['import']['stk_id']                = $stk_id;
     $response['import']['stk_name']              = $stk_name;
     $response['import']['dpn_extract_date']      = $dpn_extract_date;
     $response['import']['dpn_extract_user']      = $dpn_extract_user;
     $response['import']['smm_create_user']       = $smm_create_user;
     $response['import']['smm_create_date']       = $smm_create_date;
     $response['import']['journal_text']          = $journal_text;
     $response['import']['rowcount_original']     = $rowcount_original;
     $response['import']['rowcount_firstfound']   = $rowcount_firstfound;
     $response['import']['rowcount_completed']    = $rowcount_completed;
     $response['import']['rowcount_other']        = $rowcount_other;
     $response['import']['results']               = $arr_asset;

     // print_r($response);
     fwrite($fp, json_encode($response));
     fclose($fp);
     if (file_exists($txt_file_link)) {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.basename($txt_file_link));
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header('Content-Length: ' . filesize($txt_file_link));
         ob_clean();
         flush();
         readfile($txt_file_link);
         exit;
     }

}elseif ($act=='save_archive_stk'){
     $stkm_id = $_GET["stkm_id"];


     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql_save = "UPDATE smartdb.sm13_stk SET smm_delete_date=NOW(),smm_delete_user='$active_profile_id' WHERE stkm_id = $stkm_id;";
     echo $sql_save;
     mysqli_multi_query($con,$sql_save);
     header("Location: index.php");

}elseif ($act=='save_copy_asset'){
     $ass_id             = $_GET["ass_id"];
     $duplicate_count    = $_GET["duplicate_count"];

     $column_names = Array();
     $sql = "  SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` 
               WHERE `TABLE_SCHEMA`='smartdb' AND `TABLE_NAME`='sm14_ass';";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
          $column_names[] = $row['COLUMN_NAME'];
     }}
     unset($column_names[0]); //Remove ass_id since it is a primary key
     $field_list = implode(', ', $column_names);

     // echo "<br>".$field_list;
     // echo "<br>".$ass_id;
     // echo "<br>".$duplicate_count;
     $sql_copy = "  INSERT INTO smartdb.sm14_ass ($field_list)
                    SELECT $field_list FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
     $x=0;
     while($x < $duplicate_count) {
          // echo "<br><br>".$sql_copy;
          mysqli_multi_query($con,$sql_copy);
          $x++;
     } 

     header("Location: 10_stk.php");

}elseif ($act=='save_dearchive_stk'){
     $stkm_id = $_GET["stkm_id"];
     $sql_save = "UPDATE smartdb.sm13_stk SET smm_delete_date=null,smm_delete_user=null WHERE stkm_id = $stkm_id;";
     echo $sql_save;
     mysqli_multi_query($con,$sql_save);
     header("Location: index.php");

}elseif ($act=='save_asset_edit'){
     $ass_id             = $_POST["ass_id"];
     $res_reason_code    = $_POST["res_reason_code"];
     $res_completed      = $_POST["res_completed"];
     $fingerprint        = time();
     $sql_save = "UPDATE smartdb.sm14_ass SET res_reason_code='$res_reason_code',res_completed=$res_completed, fingerprint='$fingerprint' WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success';     
     }

     $create_user = '';
     $sql_save = "INSERT INTO smartdb.sm17_history (create_date, create_user, history_type, history_desc, history_link) VALUES (NOW(),'$create_user','Asset','Edited asset','11_ass.php?ass_id=$ass_id');";
     mysqli_multi_query($con,$sql_save);


}elseif ($act=='get_excel'){
     $stkm_id = $_GET["stkm_id"];

     $mydate=getdate(date("U"));
     $month_disp = substr("00".$mydate['mon'], -2);
     $day_disp      = substr("00".$mydate['mday'], -2);
     $hours_disp    = substr("00".$mydate['hours'], -2);
     $minutes_disp  = substr("00".$mydate['minutes'], -2);
     $seconds_disp  = substr("00".$mydate['seconds'], -2);
     $date_disp     = $mydate['year'].$month_disp.$day_disp;
     $date_disp     = $mydate['year'].$month_disp.$day_disp."_".$hours_disp.$minutes_disp.$seconds_disp;

     $txt_file_link = "$date_disp.xls";
     $file_excel    = fopen($txt_file_link, "w") or die("Unable to open file!");

     $cherry=0;
     $contents = "";
     $header   = "<html><body><table border='1'><tr>";
     $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id LIMIT 100;";
     $arr_asset = array();
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($r = $result->fetch_assoc()) {
               $contents .= "<tr>";

               if($cherry==0){
                    $cherry=1;
                    foreach($r as $column=>$value) {
                         $header .= "<td><b>$column</b></td>";
                    }
               }
               foreach($r as $column=>$value) {
                    // echo "<br>$column = $value\n";
                    $contents .= "<td>$value</td>";
               }
               $contents .= "</tr>";
     }}

     $header   .= "</tr>";
     $contents = $header.$contents."</table></body></html>";

     fwrite($file_excel, $contents);
     fclose($file_excel);
     if (file_exists($txt_file_link)) {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.basename($txt_file_link));
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header('Content-Length: ' . filesize($txt_file_link));
         ob_clean();
         flush();
         readfile($txt_file_link);
         exit;
     }


}elseif ($act=='save_asset_field'){
     $ass_id        = $_POST["ass_id"];
     $asset_vals    = $_POST["asset_vals"];

     $asset_vals = json_decode($asset_vals, true);
     $sql_list='';
     foreach ($asset_vals as $field) {
          $field_name    = $field[0];
          $field_value   = $field[1];
          $$field_name = $field[1];
          if(substr($field_name, 0,5)=="best_"||substr($field_name, 0,8)=="res_isq_"||$field_name=="res_comment"){
               $field_name = str_replace("best_", "res_", $field_name);
               if (empty($field_value)) {
                    $field_value = "null";
               }elseif($field_value==""){
                    $field_value = "null";
               }else{
                    $field_value = str_replace("'", "\'", $field_value);
                    $field_value = str_replace('"', '\"', $field_value);

                    $field_value = "'".$field_value."'";
               }
               $sql_list .= " $field_name=$field_value,";
          }
     }
     $sql_list = rtrim($sql_list,",");
     $sql_save = "  UPDATE smartdb.sm14_ass SET $sql_list WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;



}elseif ($act=='save_asset_field_single'){
    $ass_id         = $_POST["ass_id"];
    $field_name     = $_POST["field_name"];
    $best_fv        = $_POST["best_fv"];

   if (empty($best_fv)) {
        $best_fv = "null";
   }elseif($best_fv==""){
        $best_fv = "null";
   }else{
        $best_fv = str_replace("'", "\'", $best_fv);
        $best_fv = str_replace('"', '\"', $best_fv);

        $best_fv = "'".$best_fv."'";
   }
   $field_name = "res_".$field_name;
    $sql_save = "  UPDATE smartdb.sm14_ass SET $field_name=$best_fv WHERE ass_id=$ass_id";
    mysqli_multi_query($con,$sql_save);
    echo "\n".$sql_save;





}elseif ($act=='save_asset_isq'){
     $ass_id                       = $_POST["ass_id"];
     $isq                          = $_POST["isq"];
     $isq_res                      = $_POST["isq_res"];
     $res_impairment_completed     = $_POST["res_impairment_completed"];
     $res_completed                = $_POST["res_completed"];
     $sql_save = "  UPDATE smartdb.sm14_ass SET $isq=$isq_res, res_impairment_completed=$res_impairment_completed, res_completed=$res_completed WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;

}elseif ($act=='save_clear_results'){
     $ass_id        = $_GET["ass_id"];
     $sql_save = "  UPDATE smartdb.sm14_ass SET fingerprint=null, res_create_date = null, res_create_user = null, res_reason_code = null, res_reason_code_desc = null, res_impairment_completed = null, res_completed = null, res_AssetDesc1 = null, res_AssetDesc2 = null, res_AssetMainNoText = null, res_Class = null, res_classDesc = null, res_assetType = null, res_Inventory = null, res_Quantity = null, res_SNo = null, res_InventNo = null, res_accNo = null, res_Location = null, res_Room = null, res_State = null, res_latitude = null, res_longitude = null, res_CurrentNBV = null, res_AcqValue = null, res_OrigValue = null, res_ScrapVal = null, res_ValMethod = null, res_RevOdep = null, res_CapDate = null, res_LastInv = null, res_DeactDate = null, res_PlRetDate = null, res_CCC_ParentName = null, res_CCC_GrandparentName = null, res_GrpCustod = null, res_CostCtr = null, res_WBSElem = null, res_Fund = null, res_RspCCtr = null, res_CoCd = null, res_PlateNo = null, res_Vendor = null, res_Mfr = null, res_UseNo = null, res_isq_5 = null, res_isq_6 = null, res_isq_7 = null, res_isq_8 = null, res_isq_9 = null, res_isq_10 = null, res_isq_13 = null, res_isq_14 = null, res_isq_15 = null WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;

     header("Location: 11_ass.php?ass_id=".$ass_id);

}elseif ($act=='save_asset_noedit'){
     $ass_id             = $_GET["ass_id"];
     $sql_save = "UPDATE smartdb.sm14_ass SET res_reason_code='ND10',res_completed=1 WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success';     
     }
     header("Location: 10_stk.php");



}elseif ($act=='get_check_for_updates'){

}elseif ($act=='save_newfirstfound'){
     $res_reason_code    = $_POST["res_reason_code"];
     $stkm_id            = $_POST["stkm_id"];
     $asset_template     = $_POST["asset_template"];
     
     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $fingerprint        = time();
     $sql_save=" INSERT INTO smartdb.sm14_ass 
     (stkm_id, create_date, create_user, stk_include, Asset, genesis_cat, first_found_flag, res_create_date, res_create_user,
     res_reason_code, res_completed, res_AssetDesc1, fingerprint) 
     VALUES('".$stkm_id."', NOW(), '".$active_profile_id."',1,'First found','First found',1,NOW(), '".$active_profile_id."','".$res_reason_code."', 1, '".$asset_template."','$fingerprint'); ";
     mysqli_multi_query($con,$sql_save);
     echo "<br><br>".$sql_save;

     $sql = "SELECT * FROM smartdb.sm14_ass ORDER BY ass_id DESC LIMIT 1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $new_ass_id    = $row["ass_id"];
     }}


     // $sql_save_history = "INSERT INTO ".$dbname.".smart_l10_history (create_date, create_user, history_type, history_desc, history_link) VALUES ( NOW(),'".$create_user."','Created a first found','Ass_id ".$last_ass_id." was created as a first found:".$ff_type."','102_asset.php?ass_id=".$last_ass_id."');";
     // mysqli_multi_query($con,$sql_save_history);







     header("Location: 11_ass.php?ass_id=".$new_ass_id);

}elseif ($act=='save_delete_first_found'){
     $ass_id             = $_GET["ass_id"];

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql_save = "UPDATE smartdb.sm14_ass SET delete_date=NOW(), delete_user='$active_profile_id' WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success';     
     }
     header("Location: 10_stk.php");

}elseif ($act=='save_photo'){

     $ass_id        = $_POST["ass_id"];
     $input  = $_POST["res_img_data"];
     // echo "<img src='".$res_img_data."' width=100%/>";

     $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = ".$ass_id."; ";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $Asset              = $row["Asset"];
               $Subnumber          = $row["Subnumber"];
               $fingerprint        = $row["fingerprint"];
     }}
     // echo "Asset: ".$Asset; 
     if ($Asset=="First found") {
          $photo_name              = "images/".$fingerprint;
     }else{
          $photo_name              = "images/".$Asset.'-'.$Subnumber;
     }
     $original_photo_name     = $photo_name;

     $counter = 1;
     $photo_name              = $photo_name.'_'.$counter.'.jpg';
     while (file_exists($photo_name)) {
          $counter++;
          $photo_name = $original_photo_name.'_'.$counter.'.jpg';
     }


     // $create_user             = "Placeholder";
     // $sql_save = "INSERT INTO ".$dbname.".smart_l07_photo (create_date, create_user, ass_id, Asset, res_img_data) VALUES (NOW(),'".$create_user."','".$ass_id."','".$Asset."','".$res_img_data."'); ";
     // // echo $sql_save; 
     // mysqli_multi_query($con,$sql_save);

     // $input = 'http://images.websnapr.com/?size=size&key=Y64Q44QLt12u&url=http://google.com';
     // $output = 'google.com.jpg';
     // $output = '$Asset$Subnumber_';
     file_put_contents($photo_name, file_get_contents($input));

     header("Location: 11_ass.php?ass_id=".$ass_id);

}elseif ($act=='save_delete_photo'){
     $photo_filename     = "images/".$_GET["photo_filename"];
     $ass_id             = $_GET["ass_id"];
     echo $photo_filename;
     $myFileLink = fopen($photo_filename, 'w') or die("can't open file");
     fclose($myFileLink);
     unlink($photo_filename) or die("Couldn't delete file");

     header("Location: 11_ass.php?ass_id=".$ass_id);


}elseif ($act=='get_check_upload_rr') {
     $sql = "SELECT count(*) AS rowcount_rr FROM smartdb.sm12_rwr;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $rowcount_rr   = $row["rowcount_rr"];
     }}
     echo $rowcount_rr;


}elseif ($act=='save_rr_add') {
     $rr_id    = $_GET["rr_id"];
     $stkm_id  = $_GET["stkm_id"];

     $sql = "SELECT * FROM smartdb.sm12_rwr WHERE rr_id=$rr_id;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $Asset         = $row["Asset"];
               $AssetDesc1    = $row["AssetDesc1"];
               $Class         = $row["Class"];
               $ParentName    = $row["ParentName"];
     }}

     $create_user = "";
     $fingerprint = TIME();
     $sql_save = "INSERT INTO smartdb.sm14_ass (stkm_id, Asset, AssetDesc1, rr_id, genesis_cat, res_create_date, res_create_user, res_reason_code, res_completed, Class, res_comment, fingerprint) VALUES ('$stkm_id','$Asset','$AssetDesc1','$rr_id','Added from RR',NOW(),'$create_user','AF20',1,'$Class','Owner parent name: $ParentName', '$fingerprint'); ";
     mysqli_multi_query($con,$sql_save);

     $sql = "SELECT MAX(ass_id) AS ass_id FROM smartdb.sm14_ass;";
     // echo "<br><br>".$sql;
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $ass_id   = $row["ass_id"];
     }}

     $sql_save = "UPDATE smartdb.sm12_rwr SET rr_included=1 WHERE rr_id='$rr_id';";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);

     // $sql_save_history = "INSERT INTO ".$dbname.".smart_l10_history (create_date, create_user, history_type, history_desc, history_link) VALUES ( NOW(),'".$create_user."','Added an asset from the RR list','Ass_id ".$ass_id." was added from the RR list','102_asset.php?ass_id=".$ass_id."');";
     // mysqli_multi_query($con,$sql_save_history);

     header("Location: 11_ass.php?ass_id=".$ass_id);

}elseif ($act=='get_rawremainder_asset_count') {
     $search_term = $_POST["search_term"];
     $res02 = "";
     $rr_asset_count = 0;
     $sql = "SELECT COUNT(*) AS rr_asset_count FROM smartdb.sm12_rwr WHERE Asset LIKE '%".$search_term."%' OR InventNo LIKE '%".$search_term."%' OR  AssetDesc1 LIKE '%".$search_term."%' ;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $rr_asset_count = $row["rr_asset_count"];
     }}

     if ($rr_asset_count>0) {
          $msg_rr_count = "This search also matched <a href='14_rr.php?search_term=".$search_term."'>".$rr_asset_count."</a> results in the raw remainder dataset.";
     }else{
          $msg_rr_count = "This search did not match anything in the raw remainder dataset.";
     }

     $res02 = $msg_rr_count;
     // $res02 = $rr_asset_count;
     echo $res02;

}elseif ($act=='get_asset_list') {
     $search_term = $_GET["search_term"];     
     $ar = array();
     $sql = "  SELECT *, 
               CASE WHEN res_AssetDesc1 IS NULL THEN AssetDesc1 ELSE res_AssetDesc1 END AS best_AssetDesc1,
               CASE WHEN res_AssetDesc2 IS NULL THEN AssetDesc2 ELSE res_AssetDesc2 END AS best_AssetDesc2,
               CASE WHEN res_InventNo IS NULL THEN InventNo ELSE res_InventNo END AS best_InventNo,
               CASE WHEN res_SNo IS NULL THEN SNo ELSE res_SNo END AS best_SNo,
               CASE WHEN res_Location IS NULL THEN Location ELSE res_Location END AS best_Location,
               CASE WHEN res_Room IS NULL THEN Room ELSE res_Room END AS best_Room

               FROM smartdb.sm14_ass 

               WHERE storage_id LIKE '%$search_term%'
               OR stk_include LIKE '%$search_term%'
               OR Asset LIKE '%$search_term%'
               OR Subnumber LIKE '%$search_term%'
               OR impairment_code LIKE '%$search_term%'
               OR genesis_cat LIKE '%$search_term%'
               OR first_found_flag LIKE '%$search_term%'
               OR rr_id LIKE '%$search_term%'
               OR fingerprint LIKE '%$search_term%'
               OR res_create_date LIKE '%$search_term%'
               OR res_create_user LIKE '%$search_term%'
               OR res_reason_code LIKE '%$search_term%'
               OR res_reason_code_desc LIKE '%$search_term%'
               OR res_impairment_completed LIKE '%$search_term%'
               OR res_completed LIKE '%$search_term%'
               OR res_comment LIKE '%$search_term%'
               OR AssetDesc1 LIKE '%$search_term%'
               OR AssetDesc2 LIKE '%$search_term%'
               OR AssetMainNoText LIKE '%$search_term%'
               OR Class LIKE '%$search_term%'
               OR classDesc LIKE '%$search_term%'
               OR assetType LIKE '%$search_term%'
               OR Inventory LIKE '%$search_term%'
               OR Quantity LIKE '%$search_term%'
               OR SNo LIKE '%$search_term%'
               OR InventNo LIKE '%$search_term%'
               OR accNo LIKE '%$search_term%'
               OR Location LIKE '%$search_term%'
               OR Room LIKE '%$search_term%'
               OR State LIKE '%$search_term%'
               OR latitude LIKE '%$search_term%'
               OR longitude LIKE '%$search_term%'
               OR CurrentNBV LIKE '%$search_term%'
               OR AcqValue LIKE '%$search_term%'
               OR OrigValue LIKE '%$search_term%'
               OR ScrapVal LIKE '%$search_term%'
               OR ValMethod LIKE '%$search_term%'
               OR RevOdep LIKE '%$search_term%'
               OR CapDate LIKE '%$search_term%'
               OR LastInv LIKE '%$search_term%'
               OR DeactDate LIKE '%$search_term%'
               OR PlRetDate LIKE '%$search_term%'
               OR CCC_ParentName LIKE '%$search_term%'
               OR CCC_GrandparentName LIKE '%$search_term%'
               OR GrpCustod LIKE '%$search_term%'
               OR CostCtr LIKE '%$search_term%'
               OR WBSElem LIKE '%$search_term%'
               OR Fund LIKE '%$search_term%'
               OR RspCCtr LIKE '%$search_term%'
               OR CoCd LIKE '%$search_term%'
               OR PlateNo LIKE '%$search_term%'
               OR Vendor LIKE '%$search_term%'
               OR Mfr LIKE '%$search_term%'
               OR UseNo LIKE '%$search_term%'
               OR res_AssetDesc1 LIKE '%$search_term%'
               OR res_AssetDesc2 LIKE '%$search_term%'
               OR res_AssetMainNoText LIKE '%$search_term%'
               OR res_Class LIKE '%$search_term%'
               OR res_classDesc LIKE '%$search_term%'
               OR res_assetType LIKE '%$search_term%'
               OR res_Inventory LIKE '%$search_term%'
               OR res_Quantity LIKE '%$search_term%'
               OR res_SNo LIKE '%$search_term%'
               OR res_InventNo LIKE '%$search_term%'
               OR res_accNo LIKE '%$search_term%'
               OR res_Location LIKE '%$search_term%'
               OR res_Room LIKE '%$search_term%'
               OR res_State LIKE '%$search_term%'
               OR res_latitude LIKE '%$search_term%'
               OR res_longitude LIKE '%$search_term%'
               OR res_CurrentNBV LIKE '%$search_term%'
               OR res_AcqValue LIKE '%$search_term%'
               OR res_OrigValue LIKE '%$search_term%'
               OR res_ScrapVal LIKE '%$search_term%'
               OR res_ValMethod LIKE '%$search_term%'
               OR res_RevOdep LIKE '%$search_term%'
               OR res_CapDate LIKE '%$search_term%'
               OR res_LastInv LIKE '%$search_term%'
               OR res_DeactDate LIKE '%$search_term%'
               OR res_PlRetDate LIKE '%$search_term%'
               OR res_CCC_ParentName LIKE '%$search_term%'
               OR res_CCC_GrandparentName LIKE '%$search_term%'
               OR res_GrpCustod LIKE '%$search_term%'
               OR res_CostCtr LIKE '%$search_term%'
               OR res_WBSElem LIKE '%$search_term%'
               OR res_Fund LIKE '%$search_term%'
               OR res_RspCCtr LIKE '%$search_term%'
               OR res_CoCd LIKE '%$search_term%'
               OR res_PlateNo LIKE '%$search_term%'
               OR res_Vendor LIKE '%$search_term%'
               OR res_Mfr LIKE '%$search_term%'
               OR res_UseNo LIKE '%$search_term%'

 
               LIMIT 10";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $arr = array();
               $arr["label"]       = $row["Asset"].'-'.$row["Subnumber"].':'.$row["AssetDesc1"];
               $arr["Asset"]            = $row["Asset"];
               $arr["Subnumber"]        = $row["Subnumber"];
               $arr["AssetDesc1"]       = $row["best_AssetDesc1"];
               $arr["AssetDesc2"]       = $row["best_AssetDesc2"];
               $arr["InventNo"]         = $row["best_InventNo"];
               $arr["SNo"]              = $row["best_SNo"];
               $arr["Location"]         = $row["best_Location"];
               $arr["Room"]             = $row["best_Room"];
               // $arr["res_completed"]    = $row["res_completed"];

               if ($row["res_completed"]==1) {
                    $arr["status_compl"] = "<span class='octicon octicon-check text-success'></span>";
               }else{
                    $arr["status_compl"] = "<span class='octicon octicon-x text-danger' ></span>";
               }

               $arr["value"]  = $row["ass_id"];
               $ar[]          = $arr;
     }}
     echo json_encode($ar);

}
// echo $log;


?>
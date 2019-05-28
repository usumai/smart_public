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
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=0 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=0 WHERE stkm_id = $stkm_id;";
     }else{
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=1 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=1 WHERE stkm_id = $stkm_id;";
     }
     mysqli_multi_query($con,$sql_save_stk);
     mysqli_multi_query($con,$sql_save_ass);

     header("Location: index.php");

}elseif ($act=='upload_file') {//This is the old method, we should be able to delete this once the new import process is finalised
     $dev=false;
     $target_file = $_FILES["fileToUpload"]["tmp_name"];
     $fileContents = file_get_contents($target_file);

     //This is to remove the unicode encoding on the file. It leaves two characters at the start of the file which throw an error.
     $fileContents  = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $fileContents);
     $arr_full      = json_decode($fileContents, true);
     $arr           = $arr_full['import'];
     $stk_id                  = $arr['stk_id'];
     $stk_name                = $arr['stk_name'];
     $dpn_extract_date        = $arr['dpn_extract_date'];
     $dpn_extract_user        = $arr['dpn_extract_user'];
     $smm_extract_date        = $arr['smm_extract_date'];
     $smm_extract_user        = $arr['smm_extract_user'];
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

     $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,dpn_extract_date,dpn_extract_user,smm_extract_date,smm_extract_user,rowcount_original,journal_text) VALUES ('".$stk_id."','".$stk_name."','".$dpn_extract_date."','".$dpn_extract_user."','".$smm_extract_date."','".$smm_extract_user."','".$rowcount_original."','".$journal_text."'); ";
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



     function cleanvalue($fieldvalue) {
          $fieldvalue = str_replace("'", "\'", $fieldvalue);
          $fieldvalue = str_replace('"', '\"', $fieldvalue);
          // $fieldvalue = str_replace("""", "/""", $fieldvalue);
         if ($fieldvalue=="") {
               $fieldvalue="NULL";
          }
          else{
               $fieldvalue="'".$fieldvalue."'";
          }
          return $fieldvalue;
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
     $txt_file_link = 'SMARTm_file_'.$date_disp.'.json';
     $fp = fopen($txt_file_link, 'w');

     $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id ;";
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
                   sum(CASE WHEN storage_id IS NULL AND first_found_flag <> 1 THEN 1 ELSE 0 END) AS rowcount_other
               FROM smartdb.sm14_ass";
     $result2 = $con->query($sql);
     if ($result2->num_rows > 0) {
       while($row2 = $result2->fetch_assoc()) {
           $rowcount_original      = $row2["rowcount_original"];
           $rowcount_firstfound    = $row2["rowcount_firstfound"];
           $rowcount_completed     = $row2["rowcount_completed"];
           $rowcount_other         = $row2["rowcount_other"];
     }}

     $response = array();
     $response['import']['type']                  = "stocktake_export";
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


}elseif ($act=='save_asset_edit'){
     $ass_id             = $_POST["ass_id"];
     $res_reason_code    = $_POST["res_reason_code"];
     $res_completed      = $_POST["res_completed"];
     $sql_save = "UPDATE smartdb.sm14_ass SET res_reason_code='$res_reason_code',res_completed=$res_completed WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success';     
     }



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
                    $field_value = "'".$field_value."'";
               }
               $sql_list .= " $field_name=$field_value,";
          }
     }
     $sql_list = rtrim($sql_list,",");
     $sql_save = "  UPDATE smartdb.sm14_ass SET $sql_list WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;

}elseif ($act=='save_asset_isq'){
     $ass_id        = $_POST["ass_id"];
     $isq           = $_POST["isq"];
     $isq_res       = $_POST["isq_res"];
     $sql_save = "  UPDATE smartdb.sm14_ass SET $isq=$isq_res WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;

}elseif ($act=='save_clear_results'){
     $ass_id        = $_GET["ass_id"];
     $sql_save = "  UPDATE smartdb.sm14_ass SET res_create_date = null, res_create_user = null, res_reason_code = null, res_reason_code_desc = null, res_impairment_completed = null, res_completed = null, res_AssetDesc1 = null, res_AssetDesc2 = null, res_AssetMainNoText = null, res_Class = null, res_classDesc = null, res_assetType = null, res_Inventory = null, res_Quantity = null, res_SNo = null, res_InventNo = null, res_accNo = null, res_Location = null, res_Room = null, res_State = null, res_latitude = null, res_longitude = null, res_CurrentNBV = null, res_AcqValue = null, res_OrigValue = null, res_ScrapVal = null, res_ValMethod = null, res_RevOdep = null, res_CapDate = null, res_LastInv = null, res_DeactDate = null, res_PlRetDate = null, res_CCC_ParentName = null, res_CCC_GrandparentName = null, res_GrpCustod = null, res_CostCtr = null, res_WBSElem = null, res_Fund = null, res_RspCCtr = null, res_CoCd = null, res_PlateNo = null, res_Vendor = null, res_Mfr = null, res_UseNo = null, res_isq_5 = null, res_isq_6 = null, res_isq_7 = null, res_isq_8 = null, res_isq_9 = null, res_isq_10 = null, res_isq_13 = null, res_isq_14 = null, res_isq_15 = null WHERE ass_id=$ass_id";
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

}
// echo $log;


?>
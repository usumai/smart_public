<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}
$exportFileVersion=1;
$this_version_no  = 6;
$date_version_published = "2019-10-03 00:00:00";
// Steps for relesing a new version:
// 1. Update the version info above with version number one more than current
// 2. Update the 08_version.json as per above details
// 3. Delete json and xls files from directory to stop any leaks
// 4. Push local to master using toolshelf

// echo $act;
$dbname        = "smartdb";
$addr_git      = ' "\Program Files\Git\bin\git"  ';
$log           = "<br>"."Initialising action file";
$active_user   = "";
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//CRUD
if ($act=='sys_pull_master') {
	//This file updates the local software with the currently published software

	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output .= shell_exec($addr_git.' clean  -d  -f .');
	$output .= shell_exec($addr_git.' reset --hard');  
	$output .= shell_exec($addr_git.' pull https://github.com/usumai/110_smart.git');
	echo "<pre>$output</pre>";

     mysqli_multi_query($con,$sql_save);
     
	header("Location: 05_action.php?act=sys_reset_data");

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
	$output .= shell_exec($addr_git.' remote add origin https://github.com/usumai/110_smart.git'); 
	$output .= shell_exec($addr_git.' push -u origin master');
	
	echo "<pre>$output</pre>";
	
	header("Location: index.php");



}elseif ($act=='sys_initialise') {
     $log .= "<br>"."creating database: $dbname";
     $sql_save = "CREATE DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();

}elseif ($act=='sys_reset_data') {
     $sql_save = "DROP DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     
     $log .= "<br>"."creating database: $dbname";
     $sql_save = "CREATE DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();

}elseif ($act=='sys_reset_data_minus_rr') {
     //Delete all tables except for RR

     
     $sql_save = "DROP TABLE $dbname.sm10_set, $dbname.sm11_pro, $dbname.sm13_stk, $dbname.sm14_ass, $dbname.sm15_rc, $dbname.sm16_file, $dbname.sm17_history, $dbname.sm18_impairment, $dbname.sm19_result_cats, $dbname.sm20_quarantine;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();

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


     echo "<br>Type:" .$arr['type'];


     // fnAddHist("upload", $arr['type']);

     if ($arr['type']=="stocktake") {
          $stk_id                  = $arr['stk_id'];
          $stk_name                = $arr['stk_name'];
          $dpn_extract_date        = cleanvalue($arr['dpn_extract_date']);
          $dpn_extract_user        = cleanvalue($arr['dpn_extract_user']);
          $smm_extract_date        = cleanvalue($arr['smm_extract_date']);
          $smm_extract_user        = cleanvalue($arr['smm_extract_user']);
          // $smm_extract_date        = NULL;
          // $smm_extract_user        = NULL;
          $journal_text            = $arr['journal_text'];

          $rc_orig                 = $arr['rc_orig'];
          $rc_orig_complete        = $arr['rc_orig_complete'];
          $rc_extras               = $arr['rc_extras'];

          $assets                  = $arr['results'];

          if ($dev) {
               echo "<br>stk_id:".$stk_id ."<br>stk_name:".$stk_name ."<br>dpn_extract_date:".$dpn_extract_date ."<br>dpn_extract_user:".$dpn_extract_user ."<br>smm_extract_date:".$smm_extract_date ."<br>smm_extract_user:".$smm_extract_user ."<br>journal_text:".$journal_text."<br>rc_orig:".$rc_orig ."<br>rc_orig_complete:".$rc_orig_complete."<br>rc_extras:".$rc_extras;
                    // print_r($assets) ;
          }

          // $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,dpn_extract_date,dpn_extract_user,smm_extract_date,smm_extract_user,rowcount_original,stk_type, journal_text) VALUES ('".$stk_id."','".$stk_name."',".$dpn_extract_date.",".$dpn_extract_user.",".$smm_extract_date.",".$smm_extract_user.",'".$rowcount_original."','stocktake','".$journal_text."'); ";
          $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,rc_orig,stk_type, journal_text) VALUES ('".$stk_id."','".$stk_name."','".$rc_orig."','stocktake','".$journal_text."'); ";

// echo "<br>sql_save: ".$sql_save;
          if ($dev) { echo "<br>sql_save: ".$sql_save; }
          mysqli_multi_query($con,$sql_save);
          echo "<br><br>sql_save: ".$sql_save."<br><br>";
          
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
               $sql_save=" INSERT INTO smartdb.sm14_ass ($tags) VALUES(".$ass['create_date'].",".$ass['create_user'].",".$ass['delete_date'].",".$ass['delete_user'].",".$stkm_id_new.",".$ass['storage_id'].",".$ass['stk_include'].",".$ass['Asset'].",".$ass['Subnumber'].",".$ass['genesis_cat'].",".$ass['first_found_flag'].",".$ass['rr_id'].",".$ass['fingerprint'].",".$ass['res_create_date'].",".$ass['res_create_user'].",".$ass['res_reason_code'].",".$ass['res_reason_code_desc'].",".$ass['res_completed'].",".$ass['res_comment'].",".$ass['AssetDesc1'].",".$ass['AssetDesc2'].",".$ass['AssetMainNoText'].",".$ass['Class'].",".$ass['classDesc'].",".$ass['assetType'].",".$ass['Inventory'].",".$ass['Quantity'].",".$ass['SNo'].",".$ass['InventNo'].",".$ass['accNo'].",".$ass['Location'].",".$ass['Room'].",".$ass['State'].",".$ass['latitude'].",".$ass['longitude'].",".$ass['CurrentNBV'].",".$ass['AcqValue'].",".$ass['OrigValue'].",".$ass['ScrapVal'].",".$ass['ValMethod'].",".$ass['RevOdep'].",".$ass['CapDate'].",".$ass['LastInv'].",".$ass['DeactDate'].",".$ass['PlRetDate'].",".$ass['CCC_ParentName'].",".$ass['CCC_GrandparentName'].",".$ass['GrpCustod'].",".$ass['CostCtr'].",".$ass['WBSElem'].",".$ass['Fund'].",".$ass['RspCCtr'].",".$ass['CoCd'].",".$ass['PlateNo'].",".$ass['Vendor'].",".$ass['Mfr'].",".$ass['UseNo'].",".$ass['res_AssetDesc1'].",".$ass['res_AssetDesc2'].",".$ass['res_AssetMainNoText'].",".$ass['res_Class'].",".$ass['res_classDesc'].",".$ass['res_assetType'].",".$ass['res_Inventory'].",".$ass['res_Quantity'].",".$ass['res_SNo'].",".$ass['res_InventNo'].",".$ass['res_accNo'].",".$ass['res_Location'].",".$ass['res_Room'].",".$ass['res_State'].",".$ass['res_latitude'].",".$ass['res_longitude'].",".$ass['res_CurrentNBV'].",".$ass['res_AcqValue'].",".$ass['res_OrigValue'].",".$ass['res_ScrapVal'].",".$ass['res_ValMethod'].",".$ass['res_RevOdep'].",".$ass['res_CapDate'].",".$ass['res_LastInv'].",".$ass['res_DeactDate'].",".$ass['res_PlRetDate'].",".$ass['res_CCC_ParentName'].",".$ass['res_CCC_GrandparentName'].",".$ass['res_GrpCustod'].",".$ass['res_CostCtr'].",".$ass['res_WBSElem'].",".$ass['res_Fund'].",".$ass['res_RspCCtr'].",".$ass['res_CoCd'].",".$ass['res_PlateNo'].",".$ass['res_Vendor'].",".$ass['res_Mfr'].",".$ass['res_UseNo'].",".$ass['flagTemplate']."); ";
               //  echo "<br><br>".$sql_save;
               mysqli_multi_query($con,$sql_save);
          }

          
          $sql = "UPDATE smartdb.sm14_ass SET stk_include=0 WHERE stkm_id=$stkm_id_new; ";
          runSql($sql);

          fnCalcStats($stkm_id_new);

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

          $sql_save = "UPDATE smartdb.sm10_set SET rr_count = (SELECT COUNT(*) AS rr_count FROM smartdb.sm12_rwr) WHERE smartm_id =1";
          mysqli_multi_query($con,$sql_save);
          
          // $sql_save_history = "INSERT INTO ".$dbname.".smart_l10_history (create_date, create_user, history_type, history_desc, history_link) VALUES ( NOW(),'".$current_user."','Raw remainder file upload','User uploaded raw remainder V2 file','108_rr.php');";

     }elseif ($arr['type']=="impairment") {
          $stk_id                  = $arr['isID'];
          $stk_name                = $arr['isName'];
          $dpn_extract_date        = $arr['dpn_extract_date'];
          $dpn_extract_user        = $arr['dpn_extract_user'];
          $smm_extract_date        = $arr['smm_extract_date'];
          $smm_extract_user        = $arr['smm_extract_user'];
          $journal_text            = $arr['journal_text'];
          $rowcount_original       = $arr['rowcount_original'];
          $rowcount_firstfound     = $arr['rowcount_firstfound'];
          $rowcount_other          = $arr['rowcount_other'];
          $rowcount_completed      = $arr['rowcount_completed'];


          if(empty($smm_extract_date)){
               $smm_extract_date="null";
          }else{
               $smm_extract_date="'".$smm_extract_date."'";
          }

          $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,dpn_extract_date,dpn_extract_user,smm_extract_date,smm_extract_user,rowcount_original,rowcount_firstfound, rowcount_other, rowcount_completed, stk_type, journal_text) VALUES ('".$stk_id."','".$stk_name."','".$dpn_extract_date."','".$dpn_extract_user."',".$smm_extract_date.",'".$smm_extract_user."','".$rowcount_original."','".$rowcount_firstfound."','".$rowcount_other."','".$rowcount_completed."','impairment','".$journal_text."'); ";
          if (true) { echo "<br>sql_save: ".$sql_save; }
          mysqli_multi_query($con,$sql_save);

          $sql = "SELECT * FROM smartdb.sm13_stk ORDER BY stkm_id DESC LIMIT 1;";
          $result = $con->query($sql);
          if ($result->num_rows > 0) {
               while($row = $result->fetch_assoc()) {
                    $stkm_id_new    = $row["stkm_id"];
          }}


          $assets   = $arr['results'];
          foreach($assets as $ass) {
               foreach($ass as $fieldname => $fieldvalue) {
                    $ass[$fieldname] = cleanvalue($ass[$fieldname]);
               }


               
               $sql_save=" INSERT INTO smartdb.sm18_impairment (
                    stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, fingerprint
               ) VALUES(".
               
               $stkm_id_new.",".$ass['storageID'].",".$ass['rowNo'].",".$ass['DSTRCT_CODE'].",".$ass['WHOUSE_ID'].",".$ass['SUPPLY_CUST_ID'].",".$ass['SC_ACCOUNT_TYPE'].",".$ass['STOCK_CODE'].",".$ass['ITEM_NAME'].",".$ass['STK_DESC'].",".$ass['BIN_CODE'].",".$ass['INVENT_CAT'].",".$ass['INVENT_CAT_DESC'].",".$ass['TRACKING_IND'].",".$ass['SOH'].",".$ass['TRACKING_REFERENCE'].",".$ass['LAST_MOD_DATE'].",".$ass['sampleFlag'].",".$ass['serviceableFlag'].",".$ass['isBackup'].",".$ass['isType'].",".$ass['targetID'].",".$ass['delete_date'].",".$ass['delete_user'].",".$ass['res_create_date'].",".$ass['res_update_user'].",".$ass['findingID'].",".$ass['res_comment'].",".$ass['res_evidence_desc'].",".$ass['res_unserv_date'].",".$ass['isChild'].",".$ass['res_parent_storageID'].",".$ass['fingerprint']." ); ";
               // echo "<br><br>".$sql_save;
               mysqli_multi_query($con,$sql_save);
          

          }









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



     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=$stkm_id;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $stk_id               = $row["stk_id"];
             $stk_name             = $row["stk_name"];
             $dpn_extract_date     = $row["dpn_extract_date"];
             $dpn_extract_user     = $row["dpn_extract_user"];
             $stk_type             = $row["stk_type"];
             $journal_text         = $row["journal_text"];
             $rc_orig              = $row["rc_orig"];
             $rc_orig_complete     = $row["rc_orig_complete"];
             $rc_extras            = $row["rc_extras"];
     }}




     
     $sql = "SELECT COUNT(*) AS rc_totalSent FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND flagTemplate IS NULL ";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $rc_totalSent         = $row["rc_totalSent"];
     }}




     if ($stk_type=='stocktake'){
          $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id AND delete_date IS NULL ;";
     }else{
          $sql = "SELECT *  FROM smartdb.sm18_impairment WHERE stkm_id = $stkm_id AND delete_date IS NULL ;";
     }
     $arr_asset = array();
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($r = $result->fetch_assoc()) {
             $arr_asset[] = $r;
     }}


     $stk_name_disp = substr($stk_name, 30);
     $txt_file_link = "SMARTm_".$date_disp."_$stk_name_disp.json";
     $fp = fopen($txt_file_link, 'w');

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $smm_create_user    = $row["active_profile_id"];
     }}
     $smm_create_date = $mydate['year']."-".$month_disp."-".$day_disp;

     $response = array();
     $response['import']['type']                  = $stk_type;
     $response['import']['stkm_id']               = $stkm_id;
     $response['import']['exportFileVersion']     = $exportFileVersion;
     $response['import']['stk_id']                = $stk_id;
     $response['import']['stk_name']              = $stk_name;
     $response['import']['dpn_extract_date']      = $dpn_extract_date;
     $response['import']['dpn_extract_user']      = $dpn_extract_user;
     $response['import']['smm_create_user']       = $smm_create_user;
     $response['import']['smm_create_date']       = $smm_create_date;
     $response['import']['journal_text']          = $journal_text;
     $response['import']['rc_orig']               = $rc_orig;
     $response['import']['rc_orig_complete']      = $rc_orig_complete;
     $response['import']['rc_extras']             = $rc_extras;
     $response['import']['rc_totalSent']          = $rc_totalSent;
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



















}elseif ($act=='save_archive_return'){
     $stkm_id = $_GET["stkm_id"];  

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql = "UPDATE smartdb.sm13_stk SET smm_delete_date=NOW(),smm_delete_user='$active_profile_id' WHERE stkm_id = $stkm_id;";
     // echo $sql_save;
     echo runSql($sql);

}elseif ($act=='get_menu_details'){

     $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $system_stk_type = $row["stk_type"];
         }}
     if(empty($system_stk_type)) {
         $system_stk_type = "notset";
     }
     echo $system_stk_type;



}elseif ($act=='save_toggle_stk_return'){
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
     $sql = $sql_save_stk.$sql_save_ass;
     $res = runSql($sql);
     if($res=="success"){
          $res = ($stk_include==0) ? "Included" : "Excluded";
     }else{
          $res = "failed".$res;
     }
     echo $res;
     
}elseif ($act=='get_SystemStkType'){
     // Get what the tool is configured for: stocktake, impairment or nothing
     
     $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $system_stk_type = $row["stk_type"];
         }}
     if(empty($system_stk_type)) {
         $system_stk_type = "notset";
     }
     echo $system_stk_type;

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
     $sql_save = "UPDATE smartdb.sm14_ass SET res_reason_code='$res_reason_code',res_completed=$res_completed, fingerprint='$fingerprint', res_create_date=NOW() WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success'.$sql_save;     
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
     $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id;";
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
     $fingerprint        = time();

     $sql_save = "  UPDATE smartdb.sm14_ass SET $sql_list, fingerprint='$fingerprint' WHERE ass_id=$ass_id";
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
   $fingerprint        = time();
    $sql_save = "  UPDATE smartdb.sm14_ass SET $field_name=$best_fv, fingerprint='$fingerprint' WHERE ass_id=$ass_id";
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
     $sql_save = "  UPDATE smartdb.sm14_ass SET fingerprint=null, res_create_date = null, res_create_user = null, res_reason_code = null, res_reason_code_desc = null, res_impairment_completed = null, res_completed = null, res_AssetDesc1 = null, res_AssetDesc2 = null, res_AssetMainNoText = null, res_Class = null, res_classDesc = null, res_assetType = null, res_Inventory = null, res_Quantity = null, res_SNo = null, res_InventNo = null, res_accNo = null, res_Location = null, res_Room = null, res_State = null, res_latitude = null, res_longitude = null, res_CurrentNBV = null, res_AcqValue = null, res_OrigValue = null, res_ScrapVal = null, res_ValMethod = null, res_RevOdep = null, res_CapDate = null, res_LastInv = null, res_DeactDate = null, res_PlRetDate = null, res_CCC_ParentName = null, res_CCC_GrandparentName = null, res_GrpCustod = null, res_CostCtr = null, res_WBSElem = null, res_Fund = null, res_RspCCtr = null, res_CoCd = null, res_PlateNo = null, res_Vendor = null, res_Mfr = null, res_UseNo = null WHERE ass_id=$ass_id";
     mysqli_multi_query($con,$sql_save);
     echo "\n".$sql_save;

     header("Location: 11_ass.php?ass_id=".$ass_id);

}elseif ($act=='save_asset_noedit'){
     $ass_id             = $_GET["ass_id"];
     $fingerprint        = time();
     $sql_save = "UPDATE smartdb.sm14_ass SET res_reason_code='ND10',res_completed=1,fingerprint='$fingerprint', res_create_date=NOW() WHERE ass_id = $ass_id;";
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

     $create_user = "";
     $fingerprint = TIME();

     $sql = " INSERT INTO smartdb.sm14_ass (create_date, create_user,
     stkm_id, Asset, AssetDesc1, rr_id, genesis_cat, res_create_date, res_create_user, res_reason_code, res_completed, Class, res_comment, fingerprint, stk_include, res_AssetDesc1, res_Class)
     SELECT Now(), '$create_user', $stkm_id, Asset, AssetDesc1, rr_id, 'Added from RR', Now(), '$create_user', 'AF20', 1, Class, ParentName, '$fingerprint', 1,  AssetDesc1, Class  FROM smartdb.sm12_rwr WHERE rr_id=$rr_id;";
     // echo $sql;
     mysqli_multi_query($con,$sql);

     $sql = "SELECT MAX(ass_id) AS ass_id FROM smartdb.sm14_ass;";
     // echo "<br><br>".$sql;
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $ass_id   = $row["ass_id"];
     }}

     $sql_save = "UPDATE smartdb.sm12_rwr SET rr_included=1 WHERE rr_id='$rr_id';";
     // echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);
     fnCalcStats($stkm_id);
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


     $limitsql = "(SELECT * FROM smartdb.sm14_ass WHERE stkm_id IN (SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include = 1 )) AS vtIncludedAssets";


     $sql = "  SELECT *, 
               CASE WHEN res_AssetDesc1 IS NULL THEN AssetDesc1 ELSE res_AssetDesc1 END AS best_AssetDesc1,
               CASE WHEN res_AssetDesc2 IS NULL THEN AssetDesc2 ELSE res_AssetDesc2 END AS best_AssetDesc2,
               CASE WHEN res_InventNo IS NULL THEN InventNo ELSE res_InventNo END AS best_InventNo,
               CASE WHEN res_SNo IS NULL THEN SNo ELSE res_SNo END AS best_SNo,
               CASE WHEN res_Location IS NULL THEN Location ELSE res_Location END AS best_Location,
               CASE WHEN res_Room IS NULL THEN Room ELSE res_Room END AS best_Room

               FROM $limitsql

               WHERE storage_id LIKE '%$search_term%'
               OR stk_include LIKE '%$search_term%'
               OR Asset LIKE '%$search_term%'
               OR Subnumber LIKE '%$search_term%'
               OR genesis_cat LIKE '%$search_term%'
               OR first_found_flag LIKE '%$search_term%'
               OR rr_id LIKE '%$search_term%'
               OR fingerprint LIKE '%$search_term%'
               OR res_create_date LIKE '%$search_term%'
               OR res_create_user LIKE '%$search_term%'
               OR res_reason_code LIKE '%$search_term%'
               OR res_reason_code_desc LIKE '%$search_term%'
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
     $sql = "  SELECT COUNT(*) AS rwrCount FROM smartdb.sm12_rwr
               WHERE Asset LIKE '%$search_term%'
               OR InventNo LIKE '%$search_term%'
               OR AssetDesc1 LIKE '%$search_term%'";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
          $arr = array();
          $arr["Asset"]       = "Raw remainder results";
          $arr["value"]       = "RR";
          $arr["Subnumber"]   = $row["rwrCount"];
          $ar[]               = $arr;
     }}
     
     echo json_encode($ar);

}elseif ($act=='save_msi_bin_stk') {
     $findingID          = $_POST["findingID"];
     $auto_storageID     = $_POST["auto_storageID"];
     $storageID          = $_POST["storageID"];
     $res_update_user    = "";
     function clnr($fieldVal){
          // echo "<br>".$fieldVal;
          if(empty($fieldVal)&&$fieldVal==''){
               $fieldVal    = 'null';
          }else{
               $fieldVal = str_replace("'","''",$fieldVal);
               $fieldVal    = "'".$fieldVal."'";
          }
          return $fieldVal;
     }



     // print_r($_POST);

     //Delete children
     $sql = "DELETE FROM smartdb.sm18_impairment WHERE res_parent_storageID='$storageID' ";
     runSql($sql);

     $fingerprint        = time();
     if($findingID==11){

          foreach ($_POST["splityRecord"] as $key => $value) {
               $splityCount   = $_POST['splityCount'][$value];
               $splityResult  = $_POST['splityResult'][$value];
               $splityDate    = $_POST['splityDate'][$value];
               // echo "<br><b>".$splityCount." - ".$splityResult." - ".$splityDate."</b>";
               if(!empty($splityDate)){
                    $splityDate = clnr($splityDate);    
               }else{
                    $splityDate = 'null';
               }
               $sql = "  INSERT INTO smartdb.sm18_impairment (
                              res_create_date,
                              res_update_user,
                              findingID, 
                              res_unserv_date, 
                              res_parent_storageID, 
                              SOH,
                              fingerprint)
                         VALUES (
                              NOW(),
                              '$res_update_user',
                              '$splityResult',
                              $splityDate,
                              '$storageID',
                              '$fingerprint',
                              ''
                              )";

               runSql($sql);
          }

          

     }




     if(!empty($_POST['res_unserv_date'])){
          $res_unserv_date = clnr($_POST['res_unserv_date']);    
     }else{
          $res_unserv_date = 'null';
     }

     $res_comment = clnr($_POST["res_comment"]);

     $sql = "UPDATE smartdb.sm18_impairment SET 
               findingID='$findingID',  
               res_comment=$res_comment,  
               res_unserv_date=$res_unserv_date,
               res_create_date=NOW(),
               fingerprint='$fingerprint'
               WHERE 
               auto_storageID='$auto_storageID' ";
     runSql($sql);



     header("Location: 16_imp.php?auto_storageID=".$auto_storageID);

}elseif ($act=='save_clear_msi_bin') {
     $auto_storageID     = $_GET["auto_storageID"];
     $storageID          = $_GET["storageID"];

     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NULL,
     res_update_user=NULL,
     findingID=NULL,  
     res_comment=NULL,  
     res_evidence_desc=NULL,
     res_unserv_date=NULL,
     fingerprint=NULL
     WHERE 
     auto_storageID='$auto_storageID' ";
     echo $sql;
     runSql($sql);

     $sql = "DELETE FROM smartdb.sm18_impairment WHERE res_parent_storageID='$storageID' ";
     runSql($sql);

     header("Location: 16_imp.php?auto_storageID=".$auto_storageID);

}elseif ($act=='save_b2r_nstr') {
     $BIN_CODE = $_GET["BIN_CODE"];
     $stkm_id  = $_GET["stkm_id"];

     $fingerprint        = time();
     // 100 indicates NSTR
     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NOW(),
     res_update_user=NULL,
     findingID=14,
     fingerprint='$fingerprint'
     WHERE BIN_CODE='$BIN_CODE' AND isType='b2r' ";
     runSql($sql);

     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");


}elseif ($act=='save_b2r_extras') {
     $BIN_CODE = $_GET["BIN_CODE"];
     $stkm_id  = $_GET["stkm_id"];

     $fingerprint        = time();
     // 101 indicates waiting for extras
     $sql = "UPDATE smartdb.sm18_impairment SET 
     findingID=15,
     fingerprint='$fingerprint'
     WHERE BIN_CODE='$BIN_CODE'  AND isType='b2r' ";
     runSql($sql);

     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

     
}elseif ($act=='save_clear_b2r') {
     $BIN_CODE       = $_GET["BIN_CODE"];
     $stkm_id        = $_GET["stkm_id"];

     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NULL,
     res_update_user=NULL,
     findingID=NULL,  
     res_comment=NULL,  
     res_evidence_desc=NULL,
     res_unserv_date=NULL,
     fingerprint=NULL
     WHERE 
     BIN_CODE='$BIN_CODE'  AND isType='b2r'";
     runSql($sql);

     echo $sql;
     $sql = "DELETE FROM smartdb.sm18_impairment WHERE BIN_CODE='$BIN_CODE' AND isChild=1 AND isType='b2r'";
     runSql($sql);

     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");



}elseif ($act=='save_b2r_add_extra') {
     $BIN_CODE           = $_POST["BIN_CODE"];
     $extraStockcode     = $_POST["extraStockcode"];
     $extraName          = $_POST["extraName"];
     $extraSOH           = $_POST["extraSOH"];
     $stkm_id            = $_POST["stkm_id"];
     $DSTRCT_CODE        = $_POST["DSTRCT_CODE"];
     $WHOUSE_ID          = $_POST["WHOUSE_ID"];
     $res_update_user='';
     $sql = "  INSERT INTO smartdb.sm18_impairment (
          res_create_date,
          res_update_user,
          stkm_id,
          BIN_CODE, 
          DSTRCT_CODE, 
          WHOUSE_ID, 
          STOCK_CODE, 
          ITEM_NAME, 
          SOH,
          isChild,
          isType,
          fingerprint)
     VALUES (
          NOW(),
          '$res_update_user',
          '$stkm_id',
          '$BIN_CODE',
          '$DSTRCT_CODE',
          '$WHOUSE_ID',
          '$extraStockcode',
          '$extraName',
          '$extraSOH',
          1,
          'b2r',
          '$fingerprint'
          )";

     runSql($sql);
     checkExtrasFinished($BIN_CODE);
     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");


}elseif ($act=='save_b2r_extra') {
     $auto_storageID     = $_POST["auto_storageID"];
     $finalResult        = $_POST["finalResult"];
     $finalResultPath    = $_POST["finalResultPath"];
     $BIN_CODE           = $_POST["BIN_CODE"];
     $stkm_id            = $_POST["stkm_id"];

     // $finalResultPath = json_decode($finalResultPath, false);
     // echo "<br>".gettype($finalResultPath);
     $sql = "UPDATE smartdb.sm18_impairment SET 
     finalResult='$finalResult',
     finalResultPath='".$finalResultPath."'
     WHERE auto_storageID='$auto_storageID' ";
     echo runSql($sql);

     checkExtrasFinished($BIN_CODE);


}elseif ($act=='save_clear_b2r_extra') {
     $auto_storageID     = $_GET["auto_storageID"];
     $BIN_CODE           = $_GET["BIN_CODE"];
     $stkm_id            = $_GET["stkm_id"];
     $sql = "UPDATE smartdb.sm18_impairment SET 
     finalResult=NULL,
     finalResultPath=NULL
     WHERE auto_storageID='$auto_storageID' ";
     echo runSql($sql);
     checkExtrasFinished($BIN_CODE);

     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_toggle_imp_backup') {
     $stkm_id       = $_GET["stkm_id"];
     $targetID      = $_GET["targetID"];
     $BIN_CODE      = $_GET["BIN_CODE"];
     $STOCK_CODE    = $_GET["STOCK_CODE"];
     $isType        = $_GET["isType"];
     $isBackup      = $_GET["isBackup"];
     
     if ($isBackup==1){
          $isBackup = 1;
     }else{
          $isBackup = "NULL";
     }


     $sql = "UPDATE smartdb.sm18_impairment SET isBackup=$isBackup WHERE targetID='$targetID' AND stkm_id='$stkm_id' ";
     if($isType=="imp"){
          $sql .= " AND STOCK_CODE='$STOCK_CODE' AND isType='imp' ";
     }else{
          $sql .= " AND BIN_CODE='$BIN_CODE' AND isType='b2r' ";
     }
     echo $sql;
     echo runSql($sql);

     header("Location: 19_toggle.php");


     


}elseif ($act=='save_createtemplatefile') {

     $sql = "  INSERT INTO smartdb.sm13_stk (stk_id, stk_name,stk_type) VALUES (0,'template','template')";
     runSql($sql);
     header("Location: index.php");


}elseif ($act=='save_add_to_template') {
     $stkm_id       = $_GET["stkm_id"];//Template id
     $ass_id        = $_GET["ass_id"];

     $fingerprint        = time();
     $sql = " INSERT INTO smartdb.sm14_ass (create_date, stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
     SELECT Now(), $stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, '$fingerprint', res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
     FROM smartdb.sm14_ass
     WHERE ass_id =$ass_id ;";
     // echo "<br><br><br>$sql";
     runSql($sql);
     header("Location: 11_ass.php?ass_id=".$ass_id);

}elseif ($act=='save_initiate_template') {
     $ass_id        = $_POST["ass_id"];
     $stkm_id       = $_POST["stkm_id"];
     $fingerprint   = time();
     echo "<br><br>ass_id:$ass_id";

     $sql = " INSERT INTO smartdb.sm14_ass (create_date, stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc,  res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
     SELECT Now(), $stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, '$fingerprint', res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
     FROM smartdb.sm14_ass WHERE ass_id = $ass_id ;";
     runSql($sql);

     echo "<br><br>$sql";
     $sql = "SELECT ass_id FROM smartdb.sm14_ass ORDER BY ass_id DESC LIMIT 1";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
          $new_ass_id	= $row["ass_id"];
     }}
     echo "<br><br>new_ass_id: $new_ass_id";
     header("Location: 11_ass.php?ass_id=".$new_ass_id);

}elseif ($act=='save_merge_initiate') {
     // Ascertain what type of stocktake it is - GA or IS
     // Get details of existing stocktakes - name, counts
     // Create new stocktake
     // Add all good rows to table
     //Show user rows which need comparison

     $cherry = 0;
     $sql = "SELECT * FROM smartdb.sm13_stk WHERE  stk_include = 1";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
               if ($cherry==0){
                    $cherry=1;
                    $stkm_id_one    = $row["stkm_id"];
                    $journal_text_a = $row["journal_text"];
               }else{
                    $stkm_id_two    = $row["stkm_id"];
                    $journal_text_b = $row["journal_text"];
               }
               $stk_id             = $row["stk_id"];
               $stk_name           = $row["stk_name"];
               $dpn_extract_date   = $row["dpn_extract_date"];
               $rowcount_original  = $row["rowcount_original"];
               $stk_type           = $row["stk_type"];
     }}

     if ($journal_text_a==$journal_text_b){
          $journal_text_m = $journal_text_a;
     }elseif(strpos($journal_text_a, $journal_text_b) !== false){
          // Check if string B is in string A
          $journal_text_m = $journal_text_a;
     }elseif(strpos($journal_text_b, $journal_text_a) !== false){
          // Check if string A is in string B
          $journal_text_m = $journal_text_b;
     }else{
          // Combine both together
          $journal_text_m = $journal_text_a.$journal_text_b;
     }

     $sql = "  INSERT INTO smartdb.sm13_stk (stk_id, stk_name,dpn_extract_date,rowcount_original,stk_type,journal_text)
     VALUES ('$stk_id','MERGE: $stk_name','$dpn_extract_date','$rowcount_original','$stk_type','$journal_text_m')";
     // echo "<br><br><br>$sql";
     runSql($sql);

     $sql = "SELECT MAX(stkm_id) AS new_stkm_id  FROM smartdb.sm13_stk";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {    
          $new_stkm_id    = $row['new_stkm_id'];   
     }}

     // #########################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################
     if ($stk_type=="impairment"){
          $sql1 = "(SELECT auto_storageID, storageID, fingerprint FROM smartdb.sm18_impairment WHERE stkm_id=$stkm_id_one) AS vtsql1";
          $sql2 = "(SELECT auto_storageID, storageID, fingerprint FROM smartdb.sm18_impairment WHERE stkm_id=$stkm_id_two) AS vtsql2";
          
          $sql_a = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2, 
                      'Full match', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          
          $sql_b = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'Only STK1 result', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_c = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,   
                      'Only STK2 result', vtsql2.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          
          $sql_d = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF match', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          
          $sql_e = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF stk1', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_f = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF stk2', vtsql2.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          
          $sql_g = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'No result', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_h = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,   
                      'Needs comparison', vtsql1.auto_storageID AS asID1, vtsql2.auto_storageID AS asID2
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NOT NULL
                      AND vtsql1.fingerprint <> vtsql2.fingerprint";
          $sql_allgood        = "$sql_a UNION $sql_b UNION $sql_c UNION $sql_d UNION $sql_e UNION $sql_f UNION $sql_g ";
          $sql_needscomparison= $sql_h;

          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $new_stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE auto_storageID IN (SELECT auto_storageID FROM ($sql_allgood) AS vt_merge_allgood);";
          // echo "<br><br><br>$sql";
          runSql($sql);


          $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
          SELECT $new_stkm_id, asID1, asID2 FROM ($sql_needscomparison) AS vtCompare;";
          // echo "<br><br><br>$sql";
          runSql($sql);
     }elseif($stk_type=="stocktake"){
          $sql1 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one) AS vtsql1";
          $sql2 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two) AS vtsql2";
          
          $sql_a = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2, 
                      'Storage match', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          $help_log = "<br><br><br><b>Storage match</b><br>$sql_a";
          $sql_b = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'Storage result - only STK1', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          $help_log.= "<br><br><br><b>Storage result - only STK1</b><br>$sql_b";
          
          $sql_c = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
                      'Storage result - only STK2', vtsql2.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          $help_log.= "<br><br><br><b>Storage result - only STK2</b><br>$sql_c";
          
          $sql_d = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF match', vtsql1.ass_id
                      FROM 
                         (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
                         (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
                      WHERE vtsql1.fingerprint = vtsql2.fingerprint";
          $help_log.= "<br><br><br><b>FF match</b><br>$sql_d";
          
          // $sql_e = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
          //                vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
          //             'FF stk1', vtsql1.ass_id
          //             FROM $sql1, $sql2 
          //             WHERE vtsql1.storage_id IS NULL
          //             AND vtsql2.storage_id IS NULL
          //             AND vtsql1.fingerprint IS NOT NULL
          //             AND vtsql2.fingerprint IS NULL";
          $sql_e = "     SELECT NULL AS stID1, fingerprint AS fp1, NULL AS stID2, NULL AS fp2,
          'FF stk1', ass_id
          FROM smartdb.sm14_ass 
          WHERE stkm_id = $stkm_id_one 
          AND storage_id IS NULL
          AND fingerprint IS NOT NULL
          AND ass_id NOT IN (
              SELECT 
              vtsql1.ass_id
              FROM
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
              WHERE vtsql1.fingerprint = vtsql2.fingerprint
          )";          
          $help_log.= "<br><br><br><b>FF stk1</b><br>$sql_e";
          
          // $sql_f = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
          //                vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
          //             'FF stk2', vtsql2.ass_id
          //             FROM $sql1, $sql2 
          //             WHERE vtsql1.storage_id IS NULL
          //             AND vtsql2.storage_id IS NULL
          //             AND vtsql1.fingerprint IS NULL
          //             AND vtsql2.fingerprint IS NOT NULL";
          $sql_f = "     SELECT NULL AS stID1, NULL AS fp1, NULL AS stID2, fingerprint AS fp2,
          'FF stk2', ass_id
          FROM smartdb.sm14_ass 
          WHERE stkm_id = $stkm_id_two 
          AND storage_id IS NULL
          AND fingerprint IS NOT NULL
          AND ass_id NOT IN (
               SELECT 
               vtsql2.ass_id
               FROM
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
               WHERE vtsql1.fingerprint = vtsql2.fingerprint
          )";
          $help_log.= "<br><br><br><b>FF stk2</b><br>$sql_f";
          
          $sql_g = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'No result', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NULL";
          $help_log.= "<br><br><br><b>No result</b><br>$sql_g";
          
          $sql_h = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
                      'Needs comparison', vtsql1.ass_id AS asID1, vtsql2.ass_id AS asID2
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NOT NULL
                      AND vtsql1.fingerprint <> vtsql2.fingerprint";
          $help_log.= "<br><br><br><b>Needs comparison</b><br>$sql_h";


          $sql_allgood        = "$sql_a UNION $sql_b UNION $sql_c UNION $sql_d UNION $sql_e UNION $sql_f UNION $sql_g ";
          $help_log.= "<br><br><br><b>sql_allgood</b>$sql_allgood";
          // $sql_allgood_disp   = "$sql_a <br><br> $sql_b <br><br> $sql_c <br><br> $sql_d <br><br> $sql_e <br><br> $sql_f <br><br> $sql_g ";
          $sql_needscomparison= $sql_h;
          $help_log.= "<br><br><br><b>sql_needscomparison</b><br>$sql_h";
          echo $help_log;
          // echo "<br><br><br>$sql_allgood_disp<br><br> $sql_h ";
          // echo "<br><br><br>$sql_allgood";

          $sql = "  INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
          SELECT create_date, create_user, delete_date, delete_user, $new_stkm_id, storage_id, 0, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
          FROM smartdb.sm14_ass
          WHERE ass_id IN (SELECT ass_id FROM ($sql_allgood) AS vt_merge_allgood);";
          // echo "<br><br><br>$sql";
          runSql($sql);


          $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
          SELECT $new_stkm_id, asID1, asID2 FROM ($sql_needscomparison) AS vtCompare;";
          // echo "<br><br><br>$sql";
          runSql($sql);
     }


     // #########################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################
     $sql = "SELECT COUNT(*) AS qCount FROM smartdb.sm20_quarantine WHERE stkm_id = '$new_stkm_id'";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {    
          $qCount    = $row['qCount'];  
     }}

     $qCount = (empty($qCount) ? 0 : $qCount);
     if ($qCount>0){
          $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=1 WHERE stkm_id = $new_stkm_id;";
     }else{
          $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=NULL WHERE stkm_id = $new_stkm_id;";
     }
     // echo "<br><br><br>$sql";
     runSql($sql);

     // header("Location: 20_merge.php?stkm_id=$new_stkm_id");

}elseif ($act=='save_merge_select') {
     $stkm_id                 = $_GET["stkm_id"];
     $q_id                    = $_GET["q_id"];
     $selected_auto_storageID = $_GET["selected_auto_storageID"];

     $sql = "  UPDATE smartdb.sm20_quarantine SET complete_date = NOW(), selected_auto_storageID=$selected_auto_storageID WHERE q_id = $q_id;";
     // echo "<br><br><br>$sql";
     runSql($sql);
     
     header("Location: 20_merge.php?stkm_id=$stkm_id");



}elseif ($act=='save_merge_finalise') {
     $stkm_id                 = $_GET["stkm_id"];

     $sql = "SELECT * FROM smartdb.sm13_stk WHERE  stk_include = 1";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
               $stk_type           = $row["stk_type"];
     }}



     if($stk_type=="impairment"){
          $sub = "SELECT selected_auto_storageID FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id";
          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE auto_storageID IN ($sub)";
          
     }elseif($stk_type=="stocktake"){
          $sub = "SELECT selected_auto_storageID FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id";
          $sql = "  INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
          SELECT create_date, create_user, delete_date, delete_user, $stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
          FROM smartdb.sm14_ass
          WHERE ass_id IN ($sub)";
     }
     echo "<br><br><br>$sql";
     runSql($sql);

     $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=NULL WHERE stkm_id = $stkm_id;";
     runSql($sql);


     header("Location: index.php");




}
// echo $log;

function runSql($stmt){
     global $con;
     if (!mysqli_multi_query($con,$stmt)){
          $save_error = mysqli_error($con);
          $log ='failure: '.$save_error;
     }else{
          $log ='success';     
     }
     // echo "<br><br>".$stmt."<br>".$log;
     return $log;
}


function fnAddHist($history_type, $history_desc){
     global $current_user;
     $history_type = cleanvalue($history_type);
     $history_desc = cleanvalue($history_desc);
     $sql = "INSERT INTO smartdb.sm17_history (create_date, create_user, history_type, history_desc) VALUES ( NOW(),'$current_user','$history_type','$history_desc');";
     runSql($sql)
}


function cleanvalue($fieldvalue) {
     // $fieldvalue = str_replace("'", "\'", $fieldvalue);
     // $fieldvalue = str_replace('"', '\"', $fieldvalue);
     $fieldvalue = str_replace("'", "''", $fieldvalue);
     $fieldvalue = str_replace('"', '""', $fieldvalue);
     // $fieldvalue = str_replace("""", "/""", $fieldvalue);
     if ($fieldvalue=="") {
          $fieldvalue="NULL";
     }elseif (empty($fieldvalue)) {
          $fieldvalue="NULL";
     }elseif ($fieldvalue=="NULL") {
          $fieldvalue="NULL";
     }elseif ($fieldvalue=="null") {
          $fieldvalue="NULL";
     }elseif (strlen($fieldvalue)==0) {
          $fieldvalue="NULL";
     }else{
          $fieldvalue="'".$fieldvalue."'";
     }
     return $fieldvalue;
}

function checkExtrasFinished($BIN_CODE){
     global $con;

     $fingerprint        = time();
     $sql = "SELECT COUNT(*) AS extraCount, SUM(CASE WHEN finalResult IS NULL THEN 0 ELSE 1 END) AS extraComplete FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE' AND isChild=1 AND isType='b2r'";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {    
          $extraCount    = $row['extraCount']; 
          $extraComplete = $row['extraComplete'];  
     }}
     if($extraCount==$extraComplete){
          $sql = "UPDATE smartdb.sm18_impairment SET 
          findingID=16,
          fingerprint='$fingerprint'
          WHERE BIN_CODE='$BIN_CODE' ";
     }else{
          $sql = "UPDATE smartdb.sm18_impairment SET 
          findingID=15
          WHERE BIN_CODE='$BIN_CODE' ";
     }
     runSql($sql);
}


function fnCalcStats($stkm_id){
     global $con;

     $sql_rc_orig = "SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_orig FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";

     $sql_rc_orig_complete = "SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL  AND res_reason_code IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";

     $sql_rc_extras = "SELECT SUM(CASE WHEN  first_found_flag=1 AND flagTemplate IS NULL THEN 1 WHEN rr_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_extras FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";


     $sql_save = "UPDATE smartdb.sm13_stk SET 
          rc_orig=($sql_rc_orig),
          rc_orig_complete=($sql_rc_orig_complete),
          rc_extras=($sql_rc_extras)
          WHERE stkm_id = $stkm_id;";
     // echo $sql_save;
     mysqli_multi_query($con,$sql_save);

}



function fnInitiateDatabase(){
     global $con, $dbname,$this_version_no,$date_version_published,$addr_git,$log;


     $sql_save = "CREATE TABLE $dbname.sm10_set (
          `smartm_id` INT(11) NOT NULL AUTO_INCREMENT,
          `create_date` DATETIME NULL DEFAULT NULL,
          `delete_date` DATETIME NULL DEFAULT NULL,
          `update_date` DATETIME NULL DEFAULT NULL,
          `active_profile_id` INT NULL DEFAULT NULL,
          `last_access_date` DATETIME NULL,
          `last_access_profile_id` INT(11) NULL,
          `rr_extract_date` DATETIME NULL, 
          `rr_extract_user` VARCHAR(255) NULL DEFAULT NULL,
          `rr_count` INT(11) NULL,
          `journal_id` INT(11) NULL,
          `help_shown` INT(11) NULL,
          `theme_type` INT(11) NULL,
          `versionLocal` INT(11) NULL,
          `versionRemote` INT(11) NULL,
          `date_last_update_check` DATETIME NULL, 
          PRIMARY KEY (`smartm_id`),UNIQUE INDEX `smartm_id_UNIQUE` (`smartm_id` ASC));";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "INSERT INTO $dbname.sm10_set (create_date, update_date, last_access_date, journal_id, help_shown, theme_type, versionLocal, versionRemote, date_last_update_check) VALUES (NOW(), NOW(), NOW(),1,0,0, $this_version_no, $this_version_no, '$date_version_published'); ";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm11_pro (`profile_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`profile_name` VARCHAR(255) NULL DEFAULT NULL,`profile_drn` VARCHAR(255) NULL DEFAULT NULL,`profile_phone_number` VARCHAR(255) NULL DEFAULT NULL,`profile_pic` LONGTEXT NULL DEFAULT NULL,`profile_color_a` VARCHAR(255) NULL DEFAULT NULL,`profile_color_b` VARCHAR(255) NULL DEFAULT NULL,PRIMARY KEY (`profile_id`),UNIQUE INDEX `profile_id_UNIQUE` (`profile_id` ASC));";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm12_rwr (`rr_id` INT(11) NOT NULL AUTO_INCREMENT,`Asset` VARCHAR(15) NULL DEFAULT NULL,`accNo` VARCHAR(5) NULL DEFAULT NULL, `InventNo` VARCHAR(30) NULL DEFAULT NULL, `AssetDesc1` VARCHAR(255) NULL DEFAULT NULL, `Class` VARCHAR(255) NULL DEFAULT NULL, `ParentName` VARCHAR(255) NULL DEFAULT NULL, `rr_included` int(11) DEFAULT NULL, PRIMARY KEY (`rr_id`),UNIQUE INDEX `rr_id_UNIQUE` (`rr_id` ASC));";
     mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm13_stk (
          `stkm_id` INT NOT NULL AUTO_INCREMENT,
          `stk_id` INT NULL,
          `stk_name` VARCHAR(255) NULL,
          `dpn_extract_date` DATETIME NULL,
          `dpn_extract_user` VARCHAR(255) NULL,`smm_extract_date` DATETIME NULL,
          `smm_extract_user` VARCHAR(255) NULL,
          `smm_delete_date` DATETIME NULL,
          `smm_delete_user` VARCHAR(255) NULL,
          `stk_include` INT NULL,
          `rc_orig` INT NULL,
          `rc_orig_complete` INT NULL,
          `rc_extras` INT NULL,
          `stk_type` VARCHAR(255) NULL, 
          `journal_text` LONGTEXT NULL,
          `merge_lock` INT NULL, 
          
          PRIMARY KEY (`stkm_id`),
          UNIQUE INDEX `stkm_id_UNIQUE` (`stkm_id` ASC));";
     mysqli_multi_query($con,$sql_save);



     $log .= "<br>"."creating $dbname.sm14_ass ";
     $sql_save = "CREATE TABLE `$dbname`.`sm14_ass` (
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

               `genesis_cat` varchar(255) DEFAULT NULL,
               `first_found_flag` int(11) DEFAULT NULL,
               `rr_id` int(11) DEFAULT NULL,
               `fingerprint` varchar(255) DEFAULT NULL,

               `res_create_date` datetime DEFAULT NULL,
               `res_create_user` varchar(255) DEFAULT NULL,
               `res_reason_code` varchar(255) DEFAULT NULL,
               `res_reason_code_desc` varchar(255) DEFAULT NULL,
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

               `flagTemplate` int(11) DEFAULT NULL,

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

     // $sql_save = "CREATE TABLE $dbname.sm20_is (`auto_isID` INT(11) NOT NULL AUTO_INCREMENT, `isID` INT(11), `create_date` DATETIME NULL,`create_user` VARCHAR(255) NULL,`isName` VARCHAR(255) NULL, `dpn_extract_date` DATETIME NULL, `dpn_extract_user` VARCHAR(255) NULL, `rowcount_original` INT(11), `rowcount_firstfound` INT(11), `rowcount_other` INT(11), `rowcount_completed` INT(11), PRIMARY KEY (`auto_isID`));";
     // echo "<br><br>".$sql_save;
     // mysqli_multi_query($con,$sql_save);

     $sql_save = "CREATE TABLE $dbname.sm18_impairment (
          
     `auto_storageID` INT(11) NOT NULL AUTO_INCREMENT, 
     
     `stkm_id` INT(11),
     `storageID` INT(11),
     `rowNo` INT(11),
     `DSTRCT_CODE` VARCHAR(255) NULL,
     `WHOUSE_ID` VARCHAR(255) NULL,
     `SUPPLY_CUST_ID` VARCHAR(255) NULL,
     `SC_ACCOUNT_TYPE` VARCHAR(255) NULL,
     `STOCK_CODE` VARCHAR(255) NULL,
     `ITEM_NAME` VARCHAR(255) NULL,
     `STK_DESC` VARCHAR(255) NULL,
     `BIN_CODE` VARCHAR(255) NULL,
     `INVENT_CAT` VARCHAR(255) NULL,
     `INVENT_CAT_DESC` VARCHAR(255) NULL,
     `TRACKING_IND` VARCHAR(255) NULL,
     `SOH` INT(11),
     `TRACKING_REFERENCE` VARCHAR(255) NULL,
     `LAST_MOD_DATE` DATETIME NULL,

     `sampleFlag` INT(11),
     `serviceableFlag` INT(11), 
     `isBackup` INT(11),
     `isType` VARCHAR(255) NULL,
     `targetID` INT(11),

     
     
     `delete_date` datetime NULL,
     `delete_user` VARCHAR(255) NULL,

     `res_create_date` datetime NULL,
     `res_update_user` VARCHAR(255) NULL,
     `findingID` VARCHAR(255) NULL,
     `res_comment` text NULL,
     `res_evidence_desc` VARCHAR(255) NULL,
     `res_unserv_date` datetime NULL,
     `isChild` int(11) NULL,
     `res_parent_storageID` VARCHAR(255) NULL,

     `finalResult` VARCHAR(255) NULL,
     `finalResultPath` VARCHAR(255) NULL,
     `fingerprint` varchar(255) DEFAULT NULL,
     
     PRIMARY KEY (`auto_storageID`));";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);


     $sql_save = "CREATE TABLE $dbname.sm19_result_cats (
          `findingID` INT(11) NOT NULL AUTO_INCREMENT, 
          `findingName` VARCHAR(255) NULL,
          `isType` VARCHAR(30) NULL,
          `color` VARCHAR(255) NULL,
          `reqDate` INT(11),
          `reqSplit` INT(11),
          `reqComment` INT(11),
          `resAbbr` VARCHAR(30),          
          PRIMARY KEY (`findingID`));";
          echo "<br><br>".$sql_save;
          mysqli_multi_query($con,$sql_save);


     $sql_save = "INSERT INTO $dbname.sm19_result_cats (findingID, findingName, isType, color, reqDate, reqSplit, reqComment, resAbbr) VALUES 
     (1, 'Serial tracked - Item sighted - Serviceable','imp','success',0,0,0,'SER'),
     (2, 'Serial tracked - Item sighted - Unserviceable - with date','imp','success',1,0,1,'USWD'),
     (3, 'Serial tracked - Item sighted - Unserviceable - no date','imp','success',0,0,1,'USND'),
     (4, 'Serial tracked - Item not sighted - Serviceable','imp','warning',0,0,0,'SER'),
     (5, 'Serial tracked - Item not sighted - Unserviceable - with date','imp','warning',1,0,1,'USWD'),
     (6, 'Serial tracked - Item not sighted - Unserviceable - no date','imp','warning',0,0,1,'USND'),
     (7, 'Serial tracked - Item not found, no evidence provided','imp','danger',0,0,0,'NIC'),
     (8, 'Quantity tracked - Sighted or found evidence of all items - All serviceable','imp','success',0,0,0,'SER'),
     (9, 'Quantity tracked - Sighted or found evidence of all items - None serviceable - with date','imp','success',1,0,0,'USWD'),
     (10, 'Quantity tracked - Sighted or found evidence of all items - None serviceable - no date','imp','success',0,0,0,'USND'),
     (11, 'Quantity tracked - Split category - One, some or all of the following:<br>+ Not all items were found<br>+ Items were in different categories<br>+ Found more than original quantity','imp','warning',0,1,1,'SPLT'),
     (12, 'Quantity tracked - No items found, no evidence provided','imp','danger',0,0,1,'NIC'),
     (13, 'In progress - Come back to it later','imp','info',0,0,0,'TBA'),


     (14, 'No additional stockcodes were found','b2r','success',0,0,0,'NSTR'),
     (15, 'You have found some additional stockcodes but have not investigated them','b2r','info',0,0,0,'TBA'),
     (16, 'You have found some additional stockcodes and have investigated them all','b2r','warning',0,0,0,'INV')
     ; "; 
     // echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);


     $sql_save = "CREATE TABLE $dbname.sm20_quarantine (
          `q_id` INT(11) NOT NULL AUTO_INCREMENT, 
          `stkm_id` INT(11),
          `auto_storageID_one` INT(11),
          `auto_storageID_two` INT(11),
          `complete_date` datetime NULL,
          `selected_auto_storageID` INT(11),
          PRIMARY KEY (`q_id`));";
     echo "<br><br>".$sql_save;
     mysqli_multi_query($con,$sql_save);


     
     $sql_save = "UPDATE smartdb.sm10_set SET rr_count = (SELECT COUNT(*) AS rr_count FROM smartdb.sm12_rwr) WHERE smartm_id =1";
     mysqli_multi_query($con,$sql_save);


     header("Location: index.php");
}





?>
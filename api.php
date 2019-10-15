<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}

// echo $act;
$dbname = "smartdb";



if ($act=='get_system'){
    $stks = $sett = [];

    $sql = "SELECT * FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $stks[] = $row;
    }}

    $sql = "SELECT * FROM smartdb.sm10_set;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sett[] = $row;
    }}

    $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $system_stk_type = $row["stk_type"];
        }}
    if(empty($system_stk_type)) {
        $system_stk_type = "notset";
    }


    $sys  = [];
    $sys["stks"]            = $stks;
    $sys["sett"]            = $sett;
    $sys["system_stk_type"] = $system_stk_type;
    $sys = json_encode($sys);
    echo $sys;

}elseif ($act=='save_RemoveTemplate'){
     $ass_id = $_POST["ass_id"];  
     $sql = "UPDATE smartdb.sm14_ass SET delete_date=NOW(),delete_user='$active_profile_id' WHERE ass_id = $ass_id;";
     echo runSql($sql);

}elseif ($act=='get_templates'){

     $data = [];
     $sql = "SELECT ass_id, res_AssetDesc1 FROM smartdb.sm14_ass WHERE flagTemplate=1 AND delete_date IS NULL ORDER BY AssetDesc1";
     // echo $sql;
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $data[] = $row;
          // $listTmplt .= "<a class='dropdown-item' href='05_action.php?act=save_usetemplate&ass_id=$ass_id'>ASASS$res_AssetDesc1</a>";
     }}
     $data = json_encode($data);
     echo $data;

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


}elseif ($act=='save_check_version'){
     $test_internet = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
     if ($test_internet){
          $URL = 'https://raw.githubusercontent.com/usumai/110_smart/master/08_version.json';
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_URL, $URL);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          $data = curl_exec($ch);
          curl_close($ch);
          $json = json_decode($data, true);
          $latest_version_no       = $json["latest_version_no"];
          $version_publish_date    = $json["version_publish_date"];

          $sql_save = "UPDATE smartdb.sm10_set SET date_last_update_check=NOW(), versionRemote=$latest_version_no; ";
          mysqli_multi_query($con,$sql_save);
          $test_results = "Check performed";

     }else{
          $test_results = "Internet is required to check the version";
     }

     // Compare remote to local and advise if update button should be displayed
     $sql = "SELECT versionLocal, versionRemote FROM smartdb.sm10_set";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
          $versionLocal	= $row["versionLocal"];
          $versionRemote	= $row["versionRemote"];
     }}
     $data  = [];
     $data["versionLocal"]    = $versionLocal;
     $data["versionRemote"]   = $versionRemote;
     $data["test_results"]    = $test_results;
     $data = json_encode($data);
     echo $data;

}elseif ($act=='get_ImgGallery'){
     $ass_id   = $_POST["ass_id"];   

     $sql = "SELECT Asset, Subnumber, fingerprint FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
          $Asset	     = $row["Asset"];
          $Subnumber     = $row["Subnumber"];
          $fingerprint   = $row["fingerprint"];
     }}

     $a        = scandir("images/");
     $img_list = "";
     $images 	= "";
     if ($Asset=="First found") {
          $photo_name_test	= $fingerprint;
     }else{
          $photo_name_test	= $Asset.'-'.$Subnumber;
     }
     foreach ($a as $key => $photo_name) {
          $photo_name_parts = explode("_",$photo_name);
         if ($photo_name_parts[0]==$photo_name_test)  {
               $img_list .= "<button type='button' class='btn thumb_photo' value='".$photo_name."' data-toggle='modal' data-target='#modal_show_pic'><img src='images/".$photo_name."?".time()."' width='200px'></button>"; 
         }
     }
     
     echo $img_list;




}elseif ($act=='save_delete_photo'){
     $filename     = "images/".$_POST["filename"];
     echo $filename;
     $myFileLink = fopen($filename, 'w') or die("can't open file");
     fclose($myFileLink);
     unlink($filename) or die("Couldn't delete file");


}elseif ($act=='save_AssetFieldValue'){
     $fieldName     = $_POST["fieldName"];  
     $fieldValue    = $_POST["fieldValue"]; 
     $ass_id        = $_POST["ass_id"];  
     // $fieldName = $fieldName=="res_comment" ? $fieldName : "res_".$fieldName;
     // $fieldValue = $fieldValue=="##NULL##" ? "NULL" : "'".$fieldValue."'";
     // $fieldValue = strtoupper($fieldValue);
     $sql = "UPDATE smartdb.sm14_ass SET $fieldName='$fieldValue' WHERE ass_id = $ass_id;";
     // echo $sql;
     runSql($sql);

     $sql = "SELECT $fieldName, stkm_id FROM smartdb.sm14_ass  WHERE ass_id = $ass_id;";

     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $confirmedValue	= $row[$fieldName];
               $stkm_id	          = $row["stkm_id"];
     }}
     echo $confirmedValue;

     if($fieldName=="res_reason_code"){
          fnCalcStats($stkm_id);
     }

     

}elseif ($act=='save_CreateTemplateAsset') {
     $ass_id        = $_POST["ass_id"];

     $fingerprint        = time();
     $sql = " INSERT INTO smartdb.sm14_ass (create_date, stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc,  res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, flagTemplate)
     SELECT Now(), stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, '$fingerprint', res_create_date, res_create_user, res_reason_code, res_reason_code_desc,  res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, 1
     FROM smartdb.sm14_ass
     WHERE ass_id =$ass_id ;";
     echo "<br><br><br>$sql";
     echo runSql($sql);

}elseif ($act=='save_ResetAssetResults'){ 
     $ass_id        = $_POST["ass_id"];  


     $sql = "  UPDATE smartdb.sm14_ass SET 
               res_AssetDesc1 = AssetDesc1,
               res_AssetDesc2 = AssetDesc2,
               res_AssetMainNoText = AssetMainNoText,
               res_Class = Class,
               res_classDesc = classDesc,
               res_assetType = assetType,
               res_Inventory = Inventory,
               res_Quantity = Quantity,
               res_SNo = SNo,
               res_InventNo = InventNo,
               res_accNo = accNo,
               res_Location = Location,
               res_Room = Room,
               res_State = State,
               res_latitude = latitude,
               res_longitude = longitude,
               res_CurrentNBV = CurrentNBV,
               res_AcqValue = AcqValue,
               res_OrigValue = OrigValue,
               res_ScrapVal = ScrapVal,
               res_ValMethod = ValMethod,
               res_RevOdep = RevOdep,
               res_CapDate = CapDate,
               res_LastInv = LastInv,
               res_DeactDate = DeactDate,
               res_PlRetDate = PlRetDate,
               res_CCC_ParentName = CCC_ParentName,
               res_CCC_GrandparentName = CCC_GrandparentName,
               res_GrpCustod = GrpCustod,
               res_CostCtr = CostCtr,
               res_WBSElem = WBSElem,
               res_Fund = Fund,
               res_RspCCtr = RspCCtr,
               res_CoCd = CoCd,
               res_PlateNo = PlateNo,
               res_Vendor = Vendor,
               res_Mfr = Mfr,
               res_UseNo = UseNo,
               res_reason_code = NULL
               WHERE ass_id = $ass_id;";
     // echo $sql;
     runSql($sql);

     $data = [];
     $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
      while($r = $result->fetch_assoc()) {
         $data["asset"] = $r;
         $stkm_id = $r['stkm_id'];
     }}
     
     $reasoncodes = [];
     $sql = "SELECT * FROM smartdb.sm15_rc ";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
      while($r = $result->fetch_assoc()) {
         $data["reasoncodes"][] = $r;
     }}
     $data = json_encode($data);
     echo $data;
     fnCalcStats($stkm_id);

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


}











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

?>
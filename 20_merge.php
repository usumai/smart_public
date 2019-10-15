<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$stkm_id = $_GET["stkm_id"];

// $sql = "SELECT *
//         FROM
//             smartdb.sm20_quarantine AS qrtn,
//             smartdb.sm18_impairment AS im1,
//             smartdb.sm18_impairment AS im2
//         WHERE   qrtn.auto_storageID_one = im1.auto_storageID
//         AND     qrtn.auto_storageID_two = im2.auto_storageID
//         AND     stkm_id = $stkm_id";



$cherry=0;
$sql = "SELECT* FROM smartdb.sm13_stk WHERE  stk_include = 1";
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

$sql = "SELECT COUNT(*) AS incompleteCount FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id AND complete_date IS NULL";
$result = $con->query($sql);
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {     
    $incompleteCount      = $row['incompleteCount'];  
}}

$btnFinish = "<button href='#' class='btn btn-outline-dark float-right' disabled>Finalise</button>";
if ($incompleteCount==0){
    $btnFinish = "<a href='05_action.php?act=save_merge_finalise&stkm_id=$stkm_id' class='btn btn-outline-dark float-right'>Finalise</a>";
}

if ($stk_type=="impairment"){
        
    $arrFindings = array();
    $sql = "SELECT * FROM smartdb.sm19_result_cats;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {     
        $findingID      = $row['findingID'];  
        $findingName    = $row['findingName']; 
        $arrFindings[$findingID] = $findingName;
    }}

    $rws ="";
    $sql = "SELECT * FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id";
    $result2 = $con->query($sql);
    if ($result2->num_rows > 0) {
    while($row2 = $result2->fetch_assoc()) {  
        $q_id                       = $row2['q_id'];
        $auto_storageID_one         = $row2['auto_storageID_one'];  
        $auto_storageID_two         = $row2['auto_storageID_two'];
        $complete_date              = $row2['complete_date'];
        $selected_auto_storageID    = $row2['selected_auto_storageID'];

        $btnOneColor = " btn-outline-dark ";
        if ($auto_storageID_one==$selected_auto_storageID){
            $btnOneColor = " btn-dark ";
        }

        $btnTwoColor = " btn-outline-dark ";
        if ($auto_storageID_two==$selected_auto_storageID){
            $btnTwoColor = " btn-dark ";
        }

        // echo "<br>q_id:".$q_id;
        $sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $auto_storageID_one";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {    
            // DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, 
            // res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
            $DSTRCT_CODE        = $row['DSTRCT_CODE'];  
            $WHOUSE_ID          = $row['WHOUSE_ID'];  
            $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];  
            $STOCK_CODE         = $row['STOCK_CODE'];  
            $ITEM_NAME          = $row['ITEM_NAME'];  
            $STK_DESC           = $row['STK_DESC'];  
            $BIN_CODE           = $row['BIN_CODE'];  
            $isType             = $row['isType'];  
            $TRACKING_REFERENCE = $row['TRACKING_REFERENCE'];  

            $res_create_date1   = $row['res_create_date'];  
            $findingID1         = $row['findingID'];  
            $res_comment1       = $row['res_comment'];  
        }}

        $sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $auto_storageID_two";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {    
            $res_create_date2   = $row['res_create_date'];  
            $findingID2         = $row['findingID'];  
            $res_comment2       = $row['res_comment']; 
        }}

        $btnTakeOne = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_one' class='btn $btnOneColor'>Use this</a>";
        $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_two' class='btn $btnTwoColor'>Use this</a>";

        // if $complete_date


        $fOne = $arrFindings[$findingID1];
        $fTwo = $arrFindings[$findingID2];

        $rws .= "<tr>";
        $rws .= "<td>$DSTRCT_CODE<br>$WHOUSE_ID<br>$SUPPLY_CUST_ID</td>";
        $rws .= "<td>$STOCK_CODE</td>";
        $rws .= "<td>$ITEM_NAME</td>";
        $rws .= "<td>$STK_DESC</td>";
        $rws .= "<td>$BIN_CODE</td>";
        $rws .= "<td>$TRACKING_REFERENCE</td>";
        $rws .= "<td>$res_create_date1<br>$findingID1: $fOne<br>$res_comment1<br>$btnTakeOne</td>";
        $rws .= "<td>$res_create_date2<br>$findingID2: $fTwo<br>$res_comment2<br>$btnTakeTwo</td>";
        $rws .= "</tr>";

    }}
}elseif ($stk_type=="stocktake"){

    $arrRCs = array();
    $sql = "SELECT * FROM smartdb.sm15_rc;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {     
        $res_reason_code    = $row['res_reason_code'];  
        $rc_desc            = $row['rc_desc']; 
        $arrRCs[$res_reason_code] = $rc_desc;
    }}
    // echo "<br><br><br>";
    // print_r($arrRCs);
    $rws ="";
    $sql = "SELECT * FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id";
    $result2 = $con->query($sql);
    if ($result2->num_rows > 0) {
    while($row2 = $result2->fetch_assoc()) {  
        $q_id                       = $row2['q_id'];
        $auto_storageID_one         = $row2['auto_storageID_one'];  
        $auto_storageID_two         = $row2['auto_storageID_two'];
        $complete_date              = $row2['complete_date'];
        $selected_auto_storageID    = $row2['selected_auto_storageID'];

        $btnOneColor = " btn-outline-dark ";
        if ($auto_storageID_one==$selected_auto_storageID){
            $btnOneColor = " btn-dark ";
        }

        $btnTwoColor = " btn-outline-dark ";
        if ($auto_storageID_two==$selected_auto_storageID){
            $btnTwoColor = " btn-dark ";
        }

        $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $auto_storageID_one";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {    
            // ass_id, create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, impairment_code, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, res_isq_5, res_isq_6, res_isq_7, res_isq_8, res_isq_9, res_isq_10, res_isq_13, res_isq_14, res_isq_15
            $Asset              = $row['Asset'];  
            $Subnumber          = $row['Subnumber'];  
            $Class              = $row['Class'];  
            $classDesc          = $row['classDesc'];
            $AssetDesc1         = $row['AssetDesc1']; 
            $AssetDesc2         = $row['AssetDesc2']; 
            $assetType          = $row['assetType'];   

            $res_reason_codeA    = $row['res_reason_code'];  
            $res_comment1A       = $row['res_comment'];  
            $res_create_dateA    = $row['res_create_date'];
        }}

        $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $auto_storageID_two";
        // echo "<br><br><br>$sql";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {    
            $res_reason_codeB    = $row['res_reason_code'];  
            $res_comment1B       = $row['res_comment'];  
            $res_create_dateB    = $row['res_create_date'];
        }}

        $btnTakeOne = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_one' class='btn $btnOneColor'>Use this</a>";
        $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_two' class='btn $btnTwoColor'>Use this</a>";
        

        // echo "<br>A: $auto_storageID_one<br>A: $res_reason_codeA";
        // echo "<br>B: $auto_storageID_two<br>B: $res_reason_codeB";

        // $keyExists = array_key_exists($res_reason_codeA, $arrRCs);
        // echo "<br>keyExists: $keyExists";
        // $fOne = $arrRCs[$res_reason_codeA];
        // $fTwo = $arrRCs[$res_reason_codeB];
        $fOne = fnGetReasonCodeName($res_reason_codeA);
        $fTwo = fnGetReasonCodeName($res_reason_codeB);

        $rws .= "<tr>";
        $rws .= "<td>$Asset - $Subnumber</td>";
        $rws .= "<td>$Class</td>";
        $rws .= "<td>$classDesc</td>";
        $rws .= "<td>$AssetDesc1</td>";
        $rws .= "<td>$AssetDesc2</td>";
        $rws .= "<td>$assetType</td>";
        $rws .= "<td>$res_create_dateA<br>$res_reason_codeA: $fOne<br>$res_comment1A<br>$btnTakeOne</td>";
        $rws .= "<td>$res_create_dateB<br>$res_reason_codeB: $fTwo<br>$res_comment1B<br>$btnTakeTwo</td>";
        $rws .= "</tr>";



    }}
}

function fnGetReasonCodeName($res_reason_code){
    global $arrRCs;
    $keyExists = array_key_exists($res_reason_code, $arrRCs);
    if($keyExists){
        return $arrRCs[$res_reason_code];
    }else{
        return "";
    }
}

?>


<br><br><br>
<div class='row'>
    <div class='col'>
    <div class='display-4'>
        Merge deconfliction
        <?=$btnFinish?>
    </div>
        <table class='table' id='mainTable'>
            <tr>
                <th>District<br>Warehouse<br>SCA</th>
                <th>Stock code</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Bin code</th>
                <th>TrackingRef</th>
                <th>Result 1<br>Create Date<br>FindingID<br>Comment</th>
                <th>Result 2<br>Create Date<br>FindingID<br>Comment</th>
            </tr>
            <?=$rws?>
        </table>
    </div>
</div>


<?php include "04_footer.php"; ?>
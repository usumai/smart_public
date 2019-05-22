<?php include "01_dbcon.php"; ?>
<?php

$stkm_id = $_GET["stkm_id"];

$response = array();
$count = 0;
$json_ass = "";
$sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = ".$stkm_id." AND delete_date IS NULL LIMIT 10;";
$rows = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
        // $rows[] = $r;
        $json_ass .= '{';
        foreach ($r as $key => $value){
            $json_ass .= '"'.$key.'":"'.$value.'",';
        }
        $json_ass  = substr($json_ass,0,-1);
        $json_ass .= '}';

}}
echo '{"import":{"results":[';
echo $json_ass;
echo ']}}';

$sql = "SELECT * FROM test.smart_l01_settings;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $active_profile_id          = $row["active_profile_id"];
        $smartm_software_version    = $row["smartm_software_version"];
        $smartm_db_version          = $row["smartm_db_version"];
        $rr_extract_date            = $row["rr_extract_date"];
        $journal_id                 = $row["journal_id"];
}}

$sql = "SELECT * FROM test.smart_l08_journal WHERE journal_id=".$journal_id.";";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $journal_text   = $row["journal_text"];
}}

$sql = "SELECT  COUNT(*) AS count_total, SUM(CASE WHEN Asset IS NOT NULL THEN 1 ELSE 0 END) AS count_original, SUM(first_found_flag) AS count_firstfound, SUM(res_completed) AS count_completed, stk_id, stk_name FROM test.smart_lv100_results WHERE stkm_id=".$stkm_id." GROUP BY stk_id, stk_name;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count_total        = $row["count_total"];
        $count_original     = $row["count_original"];
        $count_firstfound   = $row["count_firstfound"];
        $count_completed    = $row["count_completed"];
        $stk_id             = $row["stk_id"];
        $stk_name           = $row["stk_name"];
}}

$response['import']['type']                     = "stocktake_export";
$response['import']['extract_user']             = "";
$response['import']['stkm_id']                  = $stkm_id;
$response['import']['stk_id']                   = $stk_id;
$response['import']['stk_name']                 = $stk_name;
$response['import']['active_profile_id']        = $active_profile_id;
$response['import']['smartm_software_version']  = $smartm_software_version;
$response['import']['smartm_db_version']        = $smartm_db_version;
$response['import']['rr_extract_date']          = $rr_extract_date;
$response['import']['journal_text']             = $journal_text;
$response['import']['count_total']              = $count_total;
$response['import']['count_original']           = $count_original;
$response['import']['count_firstfound']         = $count_firstfound;
$response['import']['count_completed']          = $count_completed;
$response['import']['results']                  = $rows;



?>
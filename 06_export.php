<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php

$stkm_id = $_GET["stkm_id"];

ini_set('max_execution_time', 3000); //300 seconds = 50 minutes
date_default_timezone_set('Australia/Adelaide');
date_default_timezone_set('Australia/Brisbane');
date_default_timezone_set('Australia/Broken_Hill');
date_default_timezone_set('Australia/Currie');
date_default_timezone_set('Australia/Darwin');
date_default_timezone_set('Australia/Eucla');
date_default_timezone_set('Australia/Hobart');
date_default_timezone_set('Australia/Lindeman');
date_default_timezone_set('Australia/Lord_Howe');
date_default_timezone_set('Australia/Melbourne');
date_default_timezone_set('Australia/Sydney');
date_default_timezone_set('Australia/Perth');

date_default_timezone_set('Australia/Sydney');
// File prep
// echo "<br>".date("Ymd\_His") . "<br>";
// echo "<br>".date("l jS \of F Y h:i:s A") . "<br>";
$mydate=getdate(date("U"));
$month_disp = substr("00".$mydate['mon'], -2);
$day_disp 	= substr("00".$mydate['mday'], -2);
$hours_disp 	= substr("00".$mydate['hours'], -2);
$minutes_disp 	= substr("00".$mydate['minutes'], -2);
$seconds_disp 	= substr("00".$mydate['seconds'], -2);
$date_disp = $mydate['year'].$month_disp.$day_disp;
$date_disp = $mydate['year'].$month_disp.$day_disp."_".$hours_disp.$minutes_disp.$seconds_disp;
$date_export = $mydate['year']."-".$month_disp."-".$day_disp;

$zip_file_link = 'c_exports/SMARTm_'.$date_disp.'.zip';
$txt_file_link = 'SMARTm_'.$date_disp.'.json';


$response = array();
$count = 0;

$sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = ".$stkm_id." AND delete_date IS NULL;";
$rows = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($r = $result->fetch_assoc()) {
		$rows[] = $r;
}}
// print json_encode($rows);

$sql = "SELECT * FROM test.smart_l01_settings;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$active_profile_id 			= $row["active_profile_id"];
		$smartm_software_version 	= $row["smartm_software_version"];
		$smartm_db_version 			= $row["smartm_db_version"];
		$rr_extract_date 			= $row["rr_extract_date"];
		$journal_id 				= $row["journal_id"];
}}

$sql = "SELECT * FROM test.smart_l08_journal WHERE journal_id=".$journal_id.";";
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$journal_text	= $row["journal_text"];
}}

$sql = "SELECT  COUNT(*) AS count_total, SUM(CASE WHEN Asset IS NOT NULL THEN 1 ELSE 0 END) AS count_original, SUM(first_found_flag) AS count_firstfound, SUM(res_completed) AS count_completed, stk_id, stk_name FROM test.smart_lv100_results WHERE stkm_id=".$stkm_id." GROUP BY stk_id, stk_name;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$count_total	    = $row["count_total"];
		$count_original	    = $row["count_original"];
		$count_firstfound	= $row["count_firstfound"];
		$count_completed	= $row["count_completed"];
		$stk_id	            = $row["stk_id"];
		$stk_name	        = $row["stk_name"];
}}

$response['import']['type'] 					= "stocktake_export";
$response['import']['extract_date'] 			= $date_export;
$response['import']['extract_user'] 			= "";
$response['import']['stkm_id'] 		            = $stkm_id;
$response['import']['stk_id'] 		            = $stk_id;
$response['import']['stk_name'] 		        = $stk_name;
$response['import']['active_profile_id'] 		= $active_profile_id;
$response['import']['smartm_software_version'] 	= $smartm_software_version;
$response['import']['smartm_db_version'] 		= $smartm_db_version;
$response['import']['rr_extract_date'] 			= $rr_extract_date;
$response['import']['journal_text'] 			= $journal_text;
$response['import']['export_date'] 				= $date_export;
$response['import']['count_total'] 				= $count_total;
$response['import']['count_original'] 			= $count_original;
$response['import']['count_firstfound'] 		= $count_firstfound;
$response['import']['count_completed'] 			= $count_completed;
$response['import']['results'] 					= $rows;

$fp = fopen($txt_file_link, 'w');
fwrite($fp, json_encode($response));
fclose($fp);

$zip = new ZipArchive;
if ($zip->open($zip_file_link, ZipArchive::CREATE) === TRUE)
{
    $zip->addFile($txt_file_link);
    $zip->close();
}



?>
<?php include "03_menu.php"; ?>
<style type="text/css">
pre {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}	
</style>
<div class="container">
	<div class="row">
		<h2>Stocktake</h2>
	</div>
	<div class="row">
	    <div class="col-lg">
		<!-- <a href="<?=$zip_file_link?>">Results</a> -->
		<a href="<?=$txt_file_link?>">Results</a>
		<pre><?php
		//print_r($response); 
		?></pre>
	</div>
</div>
<?php include "04_footer.php"; ?>
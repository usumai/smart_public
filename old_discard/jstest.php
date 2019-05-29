<?php include "01_dbcon.php"; ?>
<?php
$response 	= array();
$stkm_id 	= 1;

$sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = ".$stkm_id." AND delete_date IS NULL ;";
$rows = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($r = $result->fetch_assoc()) {
		$rows[] = $r;
}}

$response['import']['results'] 					= $rows;
$jsresponse = json_encode($response);

?>
<script>
"use strict";

function exportToJsonFile(jsonData) {
    let dataStr = JSON.stringify(jsonData);
    let dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    let exportFileDefaultName = 'data.json';
    
    let linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
}

var data = <?php print_r($jsresponse); ?>
	

		// {name: "cliff", age: "34"},
		// {name: "ted", age: "42"}
var jsonData = JSON.stringify(data);

function download(content, fileName, contentType) {
    var a = document.createElement("a");
    var file = new Blob([content], {type: contentType});
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}
download(jsonData, 'json.txt', 'text/plain');
</script>
<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$templates = [];
$sql = "SELECT * FROM smartdb.sm14_ass WHERE flagTemplate=1 AND delete_date IS NULL ORDER BY AssetDesc1";

$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $templates[] = $row;
}}
$templates = json_encode($templates);


?>

<script>
$( function() {
    let templates = <?=$templates?>;
    console.log(templates)
    let colGreen= "#78e090";
    let colRed  = "#FFCDD2";
    let colAmber= "#FFE0B2";
    let rwTp = ''
    for (let template in templates){
        let t = templates[template]
        let btnDelete = "<button class='btn btn-outline-danger btnRemoveTemplate' value='"+t["ass_id"]+"' >Remove</button>"
        let btnLink = "<a href='11_ass.php?ass_id="+t["ass_id"]+"' class='btn btn-outline-dark'>Link</a>"
        rwTp+="<tr id='tplrow_"+t["ass_id"]+"'>"
        rwTp+="<td>"+btnLink+"</td>"
        rwTp+="<td>"+t["fingerprint"]+"</td>"
        rwTp+="<td>"+t["create_date"]+"</td>"
        rwTp+="<td>"+t["Asset"]+"</td>"
        rwTp+="<td>"+t["Subnumber"]+"</td>"
        rwTp+="<td>"+btnDelete+"</td>"
        rwTp+="<td>"+t["res_AssetDesc1"]+"</td>"
        rwTp+="<td>"+t["res_AssetDesc2"]+"</td>"
        rwTp+="<td>"+t["res_AssetMainNoText"]+"</td>"
        rwTp+="<td>"+t["res_reason_code"]+"</td>"
        rwTp+="<td align='right'>"+btnLink+"</td>"
        rwTp+="</tr>"
    }
    $("#tblTemplates tbody").html(rwTp)

    $(document).on('click', '.btnRemoveTemplate', function(){
        let ass_id = $(this).val();
        
        $("#tplrow_"+ass_id+"").css("background-color","grey")
        console.log(ass_id)
        $.post("api.php",{
            act: "save_RemoveTemplate",
            ass_id:  ass_id
        },
        function(res, status){
            $("#tplrow_"+ass_id+"").hide()
	        fnDo("get_templates","LoadTemplates",0)
        });



        
    });





});
</script>


<br><br>
<div class='container'>
	<div class='row'>
		<div class='col'>
            <h1 class='display-4'>Templates</h1>
        </div>
	</div>
</div>

<div class='container-fluid'>
	<div class='row'>
		<div class='col'>
            <table class='table' id='tblTemplates'>
                <thead>
                    <tr>
                        <th>Link</th>
                        <th>Fingerprint</th>
                        <th>Create date</th>
                        <th>Asset</th>
                        <th>Subnumber</th>
                        <th>Remove</th>
                        <th>AssetDesc1</th>
                        <th>AssetDesc2</th>
                        <th>AssetMainNoText</th>
                        <th>Reason code</th>
                        <th>Link</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
	</div>
</div>




<?php include "04_footer.php"; ?>





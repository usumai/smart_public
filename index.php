<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$rw_stk = "";
$sql = "SELECT * FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stkm_id            = $row["stkm_id"];
        $stk_id             = $row["stk_id"];
        $stk_name           = $row["stk_name"];
        $dpn_extract_date   = $row["dpn_extract_date"];
        $dpn_extract_user   = $row["dpn_extract_user"];
        $smm_extract_date   = $row["smm_extract_date"];
        $smm_extract_user   = $row["smm_extract_user"];
        $smm_delete_date    = $row["smm_delete_date"];
        $smm_delete_user    = $row["smm_delete_user"];
        $stk_include        = $row["stk_include"];
        $journal_text       = $row["journal_text"];
        $rowcount_original  = $row["rowcount_original"];

        if ($stk_include==1) {
            $flag_included  = $icon_spot_green;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Exclude this stocktake</a>";
            $btn_archive = "";
        }else{
            $flag_included  = $icon_spot_grey;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Include this stocktake</a>";
            $btn_archive = "<a class='dropdown-item' href='05_action.php?act=save_archive_stk&stkm_id=$stkm_id'>Archive</a>";
        }
        $sql = "SELECT 
                    sum(CASE WHEN first_found_flag = 1 THEN 1 ELSE 0 END) AS rowcount_firstfound,
                    sum(CASE WHEN res_completed = 1 THEN 1 ELSE 0 END) AS rowcount_completed,
                    sum(CASE WHEN rr_id IS NOT NULL THEN 1 ELSE 0 END) AS rowcount_other
                FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL";
        $result2 = $con->query($sql);
        if ($result2->num_rows > 0) {
        while($row2 = $result2->fetch_assoc()) {
            $rowcount_firstfound    = $row2["rowcount_firstfound"];
            $rowcount_completed     = $row2["rowcount_completed"];
            $rowcount_other         = $row2["rowcount_other"];
        }}



        $btn_excel = "<a class='dropdown-item' href='05_action.php?act=get_excel&stkm_id=$stkm_id'>Output to excel</a>";
        $perc_complete = round((($rowcount_completed/($rowcount_original+$rowcount_firstfound+$rowcount_other))*100),2);
        $btn_export = "<a class='dropdown-item' href='05_action.php?act=get_export_stk&stkm_id=$stkm_id'>Export Stocktake</a>";

        $btn_action     = " <div class='dropdown'>
                                <button class='btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>
                                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                    $btn_toggle $btn_export $btn_excel $btn_archive
                                    
                                </div>
                            </div>";
        $rw_stk .= " <tr>
                        <td>$flag_included</td>
                        <td>$stk_id</td>
                        <td>$stk_name</td>
                        <td align='right'>$rowcount_original</td>
                        <td align='right'>$rowcount_completed</td>
                        <td align='right'>$perc_complete%</td>
                        <td align='right'>$rowcount_firstfound</td>
                        <td align='right'>$rowcount_other</td>
                        <td align='right'>$btn_action</td>
                    </tr>";
}}
?>

<style>
    #myProgress {
        width: 100%;
        background-color: #ddd;
    }
    #myBar {
        width: 1%;
        height: 30px;
        background-color: #4CAF50;
    }
</style>

<script type="text/javascript">
$(document).ready(function() {
    $('#area_upload_status').hide();
    $('#fileToUpload').change(function(){
        let filename = $(this).val();
        if (filename) {
            $('#btn_submit_upload').show();    
        }else{
            $('#btn_submit_upload').hide();    
        }
    });
    $('#btn_submit_upload').click(function(){
        $('#area_upload_status').show();    
        $('#form_upload').hide();
        check_upload_progress();
    });

    function check_upload_progress(){
        // do whatever you like here
        $.get( {
            url: "05_action.php",
            data: {
                act: "get_check_upload_rr"
            },
            success: function( data ) {
                console.log(data)
                $("#upload_count").text(data+" records uploaded");
            }
        });
        setTimeout(check_upload_progress, 1000);//1000 = 1 sec
    }



});
</script>
<main role="main" class="flex-shrink-0">
	<div class="container">
		<h1 class="mt-5">SMART Mobile</h1>
	</div>
</main>

<div class="container">
    <table id="table_assets" class="table">
            <tr>
                <td>Included</td>
                <td>StkNo</td>
                <td>Name</td>
                <td align='right'>Orig</td>
                <td align='right'>Completed</td>
                <td align='right'>Status</td>
                <td align='right'>FF</td>
                <td align='right'>RR</td>
                <td align='right'>Action</td>
            </tr>
        <tbody>
            <?=$rw_stk?>
        </tbody>
    </table>
    
    <form action="05_action.php" method="post" enctype="multipart/form-data" id="form_upload">
        <h5 class="card-title">Upload file</h5>
        <h6 class="card-subtitle mb-2 text-muted">Stocktake and Raw Remainder</h6>
        <p class="card-text">
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
        </p>
        <input type="hidden" name="act" value="upload_file">
        <input type="submit" value="Upload File" name="submit" class="btn btn-link" id="btn_submit_upload" style="display:none">
    </form>
    <span id="area_upload_status" style="display:none!important">
        <div class="spinner-border" role="status" id='loading_spinner' style="width: 3rem; height: 3rem;">
            <span class="sr-only" style="">Loading...</span>
        </div>
        <span id="upload_count"></span>
    </span>

    

</div>
<?php include "04_footer.php"; ?>
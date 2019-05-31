<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$sql = "SELECT  COUNT(*) as count_total, SUM(CASE WHEN res_completed=1 THEN 1 ELSE 0 END) AS count_complete  FROM smartdb.sm14_ass WHERE stk_include = 1 AND delete_date IS NULL;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count_total    = $row["count_total"];
        $count_complete = $row["count_complete"];
}}
$perc_complete = 0;
if ($count_total>0&&$count_complete>0) {
    $perc_complete = round(($count_complete/$count_total)*100,0); 
}

?>



<!-- <script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
});
</script> -->

<script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
    var table = $('#table_assets').DataTable();
    $('#table_assets').on('search.dt', function() {
        rr_search();
    }); 
    $(".btn_search_term").click(function(){
        var search_term_new = $(this).data("search_term");
        var search_term_current = $('.dataTables_filter input').val();
        table.search(search_term_current+" "+search_term_new).draw();
    });
    $(".btn_search_term_clear").click(function(){
        table.search(" ").draw();
    });
    rr_search();
    function rr_search() {
        var search_term = $('.dataTables_filter input').val();
        if (search_term.length>4) {
            $("#table_rr").html("");
            $.post("05_action.php",
            {
                act: "get_rawremainder_asset_count",
                search_term: search_term
            },
            function(data, status){
                $("#area_rr_count").html(data)
            });
        }else{
            $("#area_rr_count").html("Enter a search term greater than four characters to search the Raw Remainder dataset.")
        }
    }
});
</script>



















<div class="container-fluid">
    <br><br>
    <div class="row">
        <div class="col">
            <h2><?=$count_complete?>/<?=$count_total?> total (<?=$perc_complete?>%)&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="IS~">Add Impairment Samples filter</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="FIN~">Add completed filter</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="NYC~">Add incomplete filter</button>&nbsp;
            <button class="btn btn-warning btn_search_term_clear">Clear search terms</button></h2>
        </div>
    </div>

    <span id="area_rr_count">Enter a search term greater than four characters to search the Raw Remainder dataset.</span>
    <table id="table_assets" class="table table-sm" width="100%">
        <thead>
            <tr>
                <th>Action<br>&nbsp;</th>
                <th class="text-center">AssetID<br>Class</th>
                <!-- <th>Inventory</th> -->
                <th class="text-center">Location<br>Room</th>
                <th>Description<br>&nbsp;</th>
                <th class="text-center">InventNo<br>SerialNo</th>
                <th class="text-right">IS~<br>&nbsp;</th>
                <th class="text-right">$NBV<br>&nbsp;</th>
                <th class="text-center">Status<br>&nbsp;</th>
                <th>Action<br>&nbsp;</th>
            </tr>
        </thead>
        <tbody>





<?php

$assetType = "";

$rw_ass = "";
$sql = "SELECT ass_id, Subnumber, Class, impairment_code, CurrentNBV, res_reason_code, res_completed,
            CASE WHEN first_found_flag = 1 THEN CONCAT('FF~',fingerprint) ELSE Asset END AS best_Asset,
            CASE WHEN res_Location IS NULL THEN Location ELSE res_Location END AS best_Location,
            CASE WHEN res_Room IS NULL THEN Room ELSE res_Room END AS best_Room,
            CASE WHEN res_AssetDesc1 IS NULL THEN AssetDesc1 ELSE res_AssetDesc1 END AS best_AssetDesc1,
            CASE WHEN res_AssetDesc2 IS NULL THEN AssetDesc2 ELSE res_AssetDesc2 END AS best_AssetDesc2,
            CASE WHEN res_InventNo IS NULL THEN InventNo ELSE res_InventNo END AS best_InventNo,
            CASE WHEN res_SNo IS NULL THEN SNo ELSE res_SNo END AS best_SNo
            FROM smartdb.sm14_ass WHERE stk_include=1 AND delete_date IS NULL";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $ass_id             = $row["ass_id"];
        $Subnumber          = $row["Subnumber"];
        $Class              = $row["Class"];
        $impairment_code    = $row["impairment_code"];
        $CurrentNBV         = $row["CurrentNBV"];
        $res_reason_code    = $row["res_reason_code"];
        $res_completed      = $row["res_completed"];
        // $first_found_flag   = $row["first_found_flag"];
        $best_Asset         = $row["best_Asset"];
        $best_Location      = $row["best_Location"];
        $best_Room          = $row["best_Room"];
        $best_AssetDesc1    = $row["best_AssetDesc1"];
        $best_AssetDesc2    = $row["best_AssetDesc2"];
        $best_InventNo      = $row["best_InventNo"];
        $best_SNo           = $row["best_SNo"];

        $flag_is = "";
        if (empty($impairment_code)==false) {
            $flag_is = "IS~";
        }

        if (empty($CurrentNBV)==false) {
            $disp_CurrentNBV = "$".number_format($CurrentNBV,2);
        }else{
            $disp_CurrentNBV = 0;
        }   

        $flag_status = "<span class='text-danger'>NYC~</span>";
        if($res_completed==1){
            $flag_status = "<span class='text-success'>FIN~<br>$res_reason_code</span>";
        }
        $btn_action     = "<a href='11_ass.php?ass_id=".$ass_id."' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";

        echo "<tr><td>".$btn_action."</td><td class='text-center'>".$best_Asset."<br><small>".$assetType."-c".$Class."</small></td><td class='text-center'>".$best_Location."<br><small>".$best_Room."</small></td><td>".$best_AssetDesc1."<br><small>".$best_AssetDesc2."</small></td><td nowrap class='text-center'>".$best_InventNo."<br><small>".$best_SNo."</small></td><td class='text-center'>".$flag_is."</td><td class='text-right'>".$disp_CurrentNBV."</td><td class='text-center'>$flag_status</td><td class='text-right'>".$btn_action."</td></tr>";


}}



?>




        </tbody>
    </table>
    
</div>

<!-- 

always working on the working_development branch
pushing saves to the cloud working_development

when ready to publish
we make a final push to the working_dev branch keeping them both in sync

we change to the master branch
we merge the working branch into the master branch

we push the new master branch to remote




 -->
<?




?>
<?php include "04_footer.php"; ?>
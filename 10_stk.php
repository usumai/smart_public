<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$sql = "SELECT  COUNT(*) as count_total, SUM(CASE WHEN res_completed=1 THEN 1 ELSE 0 END) AS count_complete  FROM smartdb.sm14_ass WHERE stk_include = 1;";
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
                actionType: "get_rawremainder_asset_count",
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
                <th class="text-right">IS!<br>&nbsp;</th>
                <th class="text-right">$NBV<br>&nbsp;</th>
                <th class="text-center">Status<br>&nbsp;</th>
                <th>Action<br>&nbsp;</th>
            </tr>
        </thead>
        <tbody>





<?php

$assetType = "";

$rw_ass = "";
$sql = "SELECT ass_id, Asset, first_found_flag, Subnumber, Class, Location, Room, AssetDesc1, AssetDesc2, InventNo, SNo, impairment_code, CurrentNBV, res_reason_code, res_completed FROM smartdb.sm14_ass WHERE stk_include=1";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $ass_id             = $row["ass_id"];
        $Asset              = $row["Asset"];
        $first_found_flag   = $row["first_found_flag"];
        $Subnumber          = $row["Subnumber"];
        $Class              = $row["Class"];
        $Location           = $row["Location"];
        $Room               = $row["Room"];
        $AssetDesc1         = $row["AssetDesc1"];
        $AssetDesc2         = $row["AssetDesc2"];
        $InventNo           = $row["InventNo"];
        $SNo                = $row["SNo"];
        $impairment_code    = $row["impairment_code"];
        $CurrentNBV         = $row["CurrentNBV"];
        $res_reason_code    = $row["res_reason_code"];
        $res_completed      = $row["res_completed"];


        $curr_Location  = $Location;
        $curr_Room      = $Room;
        $curr_assetname = $AssetDesc1;
        $curr_InventNo  = $InventNo;
        $curr_SNo       = $SNo;

        if ($first_found_flag==1) {
            $asset_disp = "FirstFound";
        }else{
            $asset_disp = $Asset;
        }
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
            $flag_status = "<span class='text-success'>FIN~</span>";
        }
        $btn_action     = "<a href='11_ass.php?ass_id=".$ass_id."' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";

        echo "<tr><td>".$btn_action."</td><td class='text-center'>".$asset_disp."<br><small>".$assetType."-c".$Class."</small></td><td class='text-center'>".$curr_Location."<br><small>".$curr_Room."</small></td><td>".$curr_assetname."<br><small>".$AssetDesc2."</small></td><td nowrap class='text-center'>".$curr_InventNo."<br><small>".$curr_SNo."</small></td><td class='text-center'>".$flag_is."</td><td class='text-right'>".$disp_CurrentNBV."</td><td class='text-center'>".$flag_status."</td><td class='text-right'>".$btn_action."</td></tr>";


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
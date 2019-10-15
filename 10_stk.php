<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$sql = "SELECT  COUNT(*) as count_total, SUM(CASE WHEN res_reason_code IS NOT NULL THEN 1 ELSE 0 END) AS count_complete  FROM smartdb.sm14_ass WHERE stk_include = 1 AND delete_date IS NULL;";
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
            <button class="btn btn-primary btn_search_term" data-search_term="FF~">First found</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="FIN~">Completed</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="NYC~">Incomplete</button>&nbsp;
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
                <th class="text-right">$NBV<br>&nbsp;</th>
                <th class="text-center">Status<br>&nbsp;</th>
                <th>Action<br>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "    SELECT ass_id, Asset, Subnumber, res_Class, res_Location, res_Room, res_AssetDesc1, res_AssetDesc2, res_InventNo, res_SNo, res_CurrentNBV, res_reason_code FROM smartdb.sm14_ass WHERE stk_include=1 AND delete_date IS NULL";
        // $sql .= " LIMIT 500; ";   
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $ass_id             = $row["ass_id"];
                $Asset              = $row["Asset"];
                $Subnumber      = $row["Subnumber"];
                $res_Class          = $row["res_Class"];
                $res_Location       = $row["res_Location"];
                $res_Room           = $row["res_Room"];
                $res_AssetDesc1     = $row["res_AssetDesc1"];
                $res_AssetDesc2     = $row["res_AssetDesc2"];
                $res_InventNo       = $row["res_InventNo"];
                $res_SNo            = $row["res_SNo"];
                $res_CurrentNBV     = $row["res_CurrentNBV"];
                $res_reason_code    = $row["res_reason_code"];

                if (!empty($res_CurrentNBV)) {
                    $disp_CurrentNBV = "$".number_format($res_CurrentNBV,2);
                }else{
                    $disp_CurrentNBV = 0;
                }   

                $flag_status = "<span class='text-danger'>NYC~</span>";
                if (empty($res_reason_code)==false) {
                    $flag_status = "<span class='text-success'>FIN~<br>$res_reason_code</span>";
                }
                $btn_action = "<a href='11_ass.php?ass_id=".$ass_id."' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";
                echo "<tr><td>".$btn_action."</td><td class='text-center'>".$Asset."<br><small>c".$res_Class."</small></td><td class='text-center'>".$res_Location."<br><small>".$res_Room."</small></td><td>".$res_AssetDesc1."<br><small>".$res_AssetDesc2."</small></td><td nowrap class='text-center'>".$res_InventNo."<br><small>".$res_SNo."</small></td><td class='text-right'>".$disp_CurrentNBV."</td><td class='text-center'>$flag_status</td><td class='text-right'>".$btn_action."</td></tr>";
        }}
        ?>

        </tbody>
    </table>
    
</div>

<?php include "04_footer.php"; ?>
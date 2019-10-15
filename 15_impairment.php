<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php


function fnClNum($fv){
    $fv = (empty($fv) ? 0 : $fv);
    $fv = (is_nan($fv) ? 0 : $fv);
    return $fv;
}

function fnPerc($tot,$sub){
    $tot = fnClNum($tot);
    $sub = fnClNum($sub);
    if($tot>0){
        $perc = $sub/$tot;
        $perc = round(($perc*100),2);
    }else{
        $perc = 0;
    }
    return $perc;
}


$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";

$sql = "SELECT COUNT(*) as imp_count_total, SUM(CASE WHEN res_create_date IS NOT NULL AND delete_date IS NULL AND findingID<>13 THEN 1 ELSE 0 END) AS imp_count_complete FROM smartdb.sm18_impairment  
WHERE isType='imp' AND isBackup IS NULL AND stkm_id IN ($sqlInclude)";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $imp_count_total    = $row["imp_count_total"];
        $imp_count_complete = $row["imp_count_complete"];
}}
$imp_count_total    = fnClNum($imp_count_total);
$imp_count_complete = fnClNum($imp_count_complete);


$sql = "SELECT  COUNT(DISTINCT BIN_CODE) AS b2r_count_total FROM smartdb.sm18_impairment 
WHERE isType='b2r' AND isBackup IS NULL AND stkm_id IN ($sqlInclude) ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $b2r_count_total    = $row["b2r_count_total"];
}}
$sql = "SELECT  COUNT(DISTINCT BIN_CODE) AS b2r_count_complete
        FROM smartdb.sm18_impairment 
        WHERE isType='b2r' 
        AND isBackup IS NULL 
        AND stkm_id IN ($sqlInclude)
        AND findingID IS NOT NULL";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $b2r_count_complete = $row["b2r_count_complete"];
}}
$b2r_count_total    = fnClNum($b2r_count_total);
$b2r_count_complete = fnClNum($b2r_count_complete);

$count_total    = $imp_count_total      + $b2r_count_total;
$count_complete = $imp_count_complete   + $b2r_count_complete;
$perc_complete  = fnPerc($count_total,$count_complete)

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
            <button class="btn btn-primary btn_search_term" data-search_term="FIN~">Add completed filter</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="NYC~">Add incomplete filter</button>&nbsp;
            <button class="btn btn-warning btn_search_term_clear">Clear search terms</button></h2>
        </div>
    </div>

    <span id="area_rr_count">Enter a search term greater than four characters to search the Raw Remainder dataset.</span>
    <table id="table_assets" class="table table-sm" width="100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>DIST</th>
                <th>WHSE</th>
                <th>SCA</th>
                <th>BIN_CODE</th>
                <th>Stockcde</th>
                <th>Name</th>
                <th>Cat</th>
                <th>SOH</th>
                <th>TrkInd</th>
                <th>TrkRef</th>
                <th>Type</th>
                <th>Status</th>
                <th class='text-right'>Action</th>
            </tr>
        </thead>
        <tbody>





<?php

$assetType = "";

$rw_ass = "";






$arF = array();
$sql = "SELECT findingID, color AS fCol, resAbbr AS fAbr FROM smartdb.sm19_result_cats;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $findingID  = $row['findingID'];    
        $fCol       = $row['fCol'];   
        $fAbr       = $row['fAbr'];
        $arF['col'][$findingID] = $fCol;
        $arF['abr'][$findingID] = $fAbr;
}}



$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND isBackup IS NULL AND isType='imp'";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $stkm_id            = $row['stkm_id'];  
        $storageID          = $row['storageID'];
        $rowNo              = $row['rowNo'];
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $SC_ACCOUNT_TYPE    = $row['SC_ACCOUNT_TYPE'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $BIN_CODE           = $row['BIN_CODE'];
        $INVENT_CAT         = $row['INVENT_CAT'];
        $TRACKING_IND       = $row['TRACKING_IND'];
        $SOH                = $row['SOH'];
        $TRACKING_REFERENCE = $row['TRACKING_REFERENCE'];
        $STK_DESC           = $row['STK_DESC'];
        $sampleFlag         = $row['sampleFlag'];
        $isType             = $row['isType'];
        $res_create_date    = $row['res_create_date'];
        $findingID          = $row['findingID'];

        $flag_status = "<h4><span class='badge badge-secondary'>NYC~</span></h4>";
        if(!empty($res_create_date)){
            $fCol = $arF['col'][$findingID];
            $fAbr = $arF['abr'][$findingID];
            $flag_status = "<h4><span class='badge badge-$fCol'>FIN~$fAbr</span></h4>";
            if ($findingID==13){
                $flag_status = "<h4><span class='badge badge-$fCol'>NYC~$fAbr</span></h4>";
            }
        }
        
        $flag_type = "<h4><span class='badge badge-dark'>IMP</span></h4>";
        $btnAction = "<a href='16_imp.php?auto_storageID=$auto_storageID' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";



        echo "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."</td><td>".$WHOUSE_ID."</td><td>".$SUPPLY_CUST_ID."</td><td>".$BIN_CODE."</td><td>".$STOCK_CODE."</td><td>".$ITEM_NAME."</td><td>".substr($INVENT_CAT,0,2)."</td><td>".$SOH."</td><td>".$TRACKING_IND."</td><td>".$TRACKING_REFERENCE."</td><td>".$flag_type."</td><td>".$flag_status."</td><td class='text-right'>".$btnAction."</td></tr>";

}}






$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT  stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, findingID, SUM(SOH) AS sumSOH  FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND isBackup IS NULL AND isType='b2r' GROUP BY stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, findingID 
";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {        
        $stkm_id            = $row['stkm_id'];  
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $BIN_CODE           = $row['BIN_CODE'];
        $findingID          = $row['findingID'];
        $sumSOH             = $row['sumSOH'];

        $flag_status = "<h4><span class='badge badge-secondary'>NYC~</span></h4>";
        if(!empty($findingID)){
            $fCol = $arF['col'][$findingID];
            $fAbr = $arF['abr'][$findingID];
            $flag_status = "<h4><span class='badge badge-$fCol'>FIN~$fAbr</span></h4>";
            if ($findingID==13){
                $flag_status = "<h4><span class='badge badge-$fCol'>NYC~$fAbr</span></h4>";
            }
        }
        $flag_type = "<h4><span class='badge badge-dark'>B2R</span></h4>";
        $btnAction = "<a href='17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";



        echo "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."</td><td>".$WHOUSE_ID."</td><td>".$SUPPLY_CUST_ID."</td><td>".$BIN_CODE."</td><td></td><td></td><td></td><td>".$sumSOH."</td><td></td><td></td><td>".$flag_type."</td><td>".$flag_status."</td><td class='text-right'>".$btnAction."</td></tr>";

}}

?>




        </tbody>
    </table>
    
</div>

<?php include "04_footer.php"; ?>
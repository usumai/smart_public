<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";


$rws = '';
$sql = "SELECT isType, targetID, stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, STOCK_CODE, ITEM_NAME, isBackup, COUNT(*) AS targetItemCount FROM smartdb.sm18_impairment WHERE stkm_id IN ($sqlInclude) AND isType='imp' GROUP BY isType, targetID, stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, STOCK_CODE, ITEM_NAME, isBackup"; 
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {   
        $isType             = $row['isType'];
        $targetID           = $row['targetID']; 
        $stkm_id            = $row['stkm_id'];    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $targetItemCount    = $row['targetItemCount'];
        $isBackup           = $row['isBackup'];

        $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=1' class='btn btn-outline-dark'>Primary</a>";
        if($isBackup==1){
            $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=0' class='btn btn-dark'>Backup</a>";
        }
        
        $btnType = "<span class='badge badge-info'>IMP</span>";
        
        $rws.="<tr><td>$btnType</td><td>$targetID</td><td>$DSTRCT_CODE</td><td>$WHOUSE_ID</td><td>$SUPPLY_CUST_ID</td><td></td><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td>$targetItemCount</td><td>$btnBackup</td></tr>";
}}


$sql = "SELECT isType, targetID, stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, isBackup, COUNT(*) AS targetItemCount FROM smartdb.sm18_impairment WHERE stkm_id=1 AND isType='b2r' GROUP BY isType, targetID, stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, isBackup";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $stkm_id            = $row['stkm_id'];    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $BIN_CODE           = $row['BIN_CODE'];
        $isType             = $row['isType'];
        $targetID           = $row['targetID'];
        $targetItemCount    = $row['targetItemCount'];
        $isBackup           = $row['isBackup'];

        $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&BIN_CODE=$BIN_CODE&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=1' class='btn btn-outline-dark'>Primary</a>";
        if($isBackup==1){
            $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&BIN_CODE=$BIN_CODE&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=0' class='btn btn-dark'>Backup</a>";
        }
        
        $btnType = "<span class='badge badge-primary'>B2R</span>";
        
        $rws.="<tr><td>$btnType</td><td>$targetID</td><td>$DSTRCT_CODE</td><td>$WHOUSE_ID</td><td>$SUPPLY_CUST_ID</td><td>$BIN_CODE</td><td></td><td></td><td>$targetItemCount</td><td>$btnBackup</td></tr>";
}}

?>

<script type="text/javascript">
//Declare other global variables
let dispQtrack,dispStrack,complete;

$(document).ready(function() {
    
    $('#table_backup').DataTable({
        stateSave: true
    });
});
</script>


<style>
.list-group-item{
    margin-bottom:10px;
}
</style>



<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Toggle items between primary and backup</h1>
    </div>
</div>



<div class='row'>
    <div class='col lead'> 
        <table id='table_backup' class='table table-sm'>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>TargetID</th>
                    <th>DIST</th>
                    <th>WHSE</th>
                    <th>SCA</th>
                    <th>BIN_CODE</th>
                    <th>Stockcde</th>
                    <th>Name</th>
                    <th>Count</th>
                    <th class='text-right'>Action</th>
                </tr>
            </thead>
            <tbody>
                <?=$rws?>
            </tbody>
        </table>
    </div>
</div>


<?php include "04_footer.php"; ?>
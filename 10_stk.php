<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
});
</script>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <h1 class="mt-5">SMART Mobile</h1>
        <p class="lead">New auto updating software. Production edition</p>
    </div>
</main>

<div class="container">
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
$sql = "SELECT ass_id, Asset, first_found_flag, Subnumber, Class, Location, Room, AssetDesc1, AssetDesc2, InventNo, SNo, impairment_code, CurrentNBV, res_reason_code, res_completed FROM smartdb.sm14_ass WHERE stkm_id IN (SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND delete_date IS NULL) LIMIT 500;";
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
            $btn_asset  = "<a href='102_asset.php?ass_id=".$ass_id."'>FirstFound</a>";
            $asset_disp = "FirstFound";
        }else{
            $btn_asset  = "<a href='102_asset.php?ass_id=".$ass_id."'>".$Asset."</a>";
            $asset_disp = $Asset;

        }
        $flag_is = "";
        if (empty($impairment_code)==false) {
            $flag_is = "IS!";
        }

        if (empty($CurrentNBV)==false) {
            $disp_CurrentNBV = "$".number_format($CurrentNBV,2);
        }else{
            $disp_CurrentNBV = 0;
        }   
        $btn_status = "";
        $btn_action     = "<a href='102_asset.php?ass_id=".$ass_id."' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";

        echo "<tr><td>".$btn_action."</td><td class='text-center'>".$asset_disp."<br><small>".$assetType."-c".$Class."</small></td><td class='text-center'>".$curr_Location."<br><small>".$curr_Room."</small></td><td>".$curr_assetname."<br><small>".$AssetDesc2."</small></td><td nowrap class='text-center'>".$curr_InventNo."<br><small>".$curr_SNo."</small></td><td class='text-center'>".$flag_is."</td><td class='text-right'>".$disp_CurrentNBV."</td><td class='text-center'>".$btn_status."</td><td class='text-right'>".$btn_action."</td></tr>";


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
<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$rw_stk = "";
$sql = "SELECT * FROM smartdb.sm13_stk WHERE smm_delete_date IS NOT NULL;";
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
        if ($stk_include==1) {
            $flag_included  = $icon_spot_green;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Exclude this stocktake</a>";
        }else{
            $flag_included  = $icon_spot_grey;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Include this stocktake</a>";
        }
        $sql = "SELECT 
                    sum(CASE WHEN storage_id IS NOT NULL THEN 1 ELSE 0 END) AS rowcount_original,
                    sum(CASE WHEN first_found_flag = 1 THEN 1 ELSE 0 END) AS rowcount_firstfound,
                    sum(CASE WHEN res_completed = 1 THEN 1 ELSE 0 END) AS rowcount_completed,
                    sum(CASE WHEN storage_id IS NULL AND first_found_flag <> 1 THEN 1 ELSE 0 END) AS rowcount_other
                FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL";
        $result2 = $con->query($sql);
        if ($result2->num_rows > 0) {
        while($row2 = $result2->fetch_assoc()) {
            $rowcount_original      = $row2["rowcount_original"];
            $rowcount_firstfound    = $row2["rowcount_firstfound"];
            $rowcount_completed     = $row2["rowcount_completed"];
            $rowcount_other         = $row2["rowcount_other"];
        }}
        $btn_excel = "<a class='dropdown-item' href='05_action.php?act=get_excel&stkm_id=$stkm_id'>Output to excel</a>";
        $perc_complete = round((($rowcount_completed/$rowcount_original)*100),2);
        $btn_export = "<a class='dropdown-item' href='05_action.php?act=get_export_stk&stkm_id=$stkm_id'>Export Stocktake</a>";
        $btn_dearchive = "<a class='dropdown-item' href='05_action.php?act=save_dearchive_stk&stkm_id=$stkm_id'>Restore</a>";
        $btn_action     = " <div class='dropdown'>
                                <button class='btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>
                                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                    $btn_excel $btn_dearchive
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

<br><br>
<div class="container">
    <div class="row">
        <div class="col">
            <h2>Admin center</h2>

        </div>
    </div>


    <div class="row">
        <div class="col">

		    <table id="table_assets" class="table">
		            <tr>
		                <td>Included</td>
		                <td>StkNo</td>
		                <td>Name</td>
		                <td align='right'>Orig</td>
		                <td align='right'>Completed</td>
		                <td align='right'>Status</td>
		                <td align='right'>FF</td>
		                <td align='right'>Other</td>
		                <td align='right'>Action</td>
		            </tr>
		        <tbody>
		            <?=$rw_stk?>
		        </tbody>
		    </table>

        </div>
    </div>


</div>
<?php include "04_footer.php"; ?>
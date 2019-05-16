<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php






$rw_stk = "";
$sql = "SELECT * FROM smartdb.sm13_stk WHERE delete_date IS NULL;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stkm_id        = $row["stkm_id"];
        $stk_id         = $row["stk_id"];
        $stk_name       = $row["stk_name"];
        $extract_date   = $row["extract_date"];
        $extract_user   = $row["extract_user"];
        $stk_include    = $row["stk_include"];
        $stk_name       = str_replace("_", " ", $stk_name);

        if ($stk_include==1) {
            $flag_included  = $icon_spot_green;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Exclude this stocktake</a>";
        }else{
            $flag_included  = $icon_spot_grey;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Include this stocktake</a>";
        }

        $sql = " SELECT 
                        sum(CASE WHEN storage_id IS NOT NULL THEN 1 ELSE 0 END) AS count_original,
                        sum(CASE WHEN res_completed = 1 THEN 1 ELSE 0 END) AS count_completed,
                        sum(CASE WHEN first_found_flag = 1 THEN 1 ELSE 0 END) AS count_firstfound,
                        sum(CASE WHEN storage_id IS NULL AND first_found_flag <> 1 THEN 1 ELSE 0 END) AS count_other
                    FROM smartdb.sm14_ass";
        // // echo $sql;
        $result2 = $con->query($sql);
        if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $count_original     = $row2["count_original"];
                $count_completed    = $row2["count_completed"];
                $count_firstfound   = $row2["count_firstfound"];
                $count_other        = $row2["count_other"];
        }}

        $perc_complete = round((($count_completed/$count_original)*100),2);
        




        $btn_export = "<a class='dropdown-item' href='109_export.php?stkm_id=".$stkm_id."'>Export Stocktake</a>";

        $btn_action     = " <div class='dropdown'>
                                <button class='btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>
                                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                    $btn_toggle $btn_export
                                    <a class='dropdown-item' href='#'>Archive</a>
                                </div>
                            </div>";
        // $btn_action = "";
        $rw_stk = " <tr>
                        <td>$flag_included</td>
                        <td>$stk_id</td>
                        <td>$stk_name</td>
                        <td align='right'>$count_original</td>
                        <td align='right'>$count_completed</td>
                        <td align='right'>$perc_complete%</td>
                        <td align='right'>$count_firstfound</td>
                        <td align='right'>$count_other</td>
                        <td align='right'>$btn_action</td>
                    </tr>";




        // $site_list = "";
        // $sql = "SELECT accNo, site_name  FROM ".$dbname.".smart_l05_assets WHERE stkm_id=".$stkm_id." AND site_name IS NOT NULL GROUP BY accNo, site_name;";
        // $result2 = $con->query($sql);
        // if ($result2->num_rows > 0) {
        //     while($row2 = $result2->fetch_assoc()) {
        //         $accNo      = $row2["accNo"];
        //         $site_name  = $row2["site_name"];
        //         $site_list .= "<small><br>-".$accNo.": ".$site_name."</small>";

        // }}

        // $btn_archive_stk = "<a href='05_action.php?actionType=save_archive_stk&stkm_id=".$stkm_id."' class='btn btn-outline-danger'>Archive</a>";

        // $rows_stk      .= "<tr><td>".$stk_id."</td><td class='text-center'>".$flag_included."</td><td class='text-center'>".$flag_completed."</td><td>".$stk_name.$site_list."</td><td>".$btn_archive_stk."</td><td>".$extract_date."</td><td>".$asset_count_fin."/".$asset_count."</td><td align='right'>".$btn_export."</td></tr>";

}}

?>

<script type="text/javascript">
// $(document).ready(function() {
//     $('#table_assets').DataTable({
//         stateSave: true
//     });
// });
</script>

<main role="main" class="flex-shrink-0">
	<div class="container">
		<h1 class="mt-5">SMART Mobile</h1>
		<p class="lead">New auto updating software. Production edition</p>
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
                <td align='right'>Other</td>
                <td align='right'>Action</td>
            </tr>
        <tbody>
            <?=$rw_stk?>
        </tbody>
    </table>
    



    <form action="05_action.php" method="post" enctype="multipart/form-data">
        <h5 class="card-title">Upload file</h5>
        <h6 class="card-subtitle mb-2 text-muted">Stocktake and Raw Remainder</h6>
        <p class="card-text">
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
        </p>
        <input type="hidden" name="act" value="upload_file">
        <input type="submit" value="Upload File" name="submit" class="btn btn-link">
    </form>



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
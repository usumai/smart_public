<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<br><br>
<?php

$rows = '';

// print_r($json);

?>



<!-- <script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
});
</script> -->

<script type="text/javascript">
    // var dataSet = [<?=$rows?>];



 
var dataSet = [
    <?php
        $sql = "SELECT ass_id, Asset, first_found_flag, Subnumber, Class, Location, Room, AssetDesc1, AssetDesc2, InventNo, SNo, impairment_code, CurrentNBV, res_reason_code, res_completed FROM smartdb.sm14_ass WHERE stk_include=1 AND delete_date IS NULL";
        $sql = "SELECT ass_id, Asset, first_found_flag, Subnumber, Class, Location FROM smartdb.sm14_ass WHERE stk_include=1 AND delete_date IS NULL";
        // $sql .= " LIMIT 50; ";   
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "['".$row["ass_id"]."','".$row["Asset"]."','".$row["first_found_flag"]."','".$row["Subnumber"]."','".$row["Class"]."','".$row["Location"]."'],";
        }}
?>
];



$(document).ready(function() {
    $('#example').DataTable( {
        data: dataSet,
        columns: [
            { title: "Name" },
            { title: "Position" },
            { title: "Office" },
            { title: "Extn." },
            { title: "Start date" },
            { title: "Salary" }
        ]
    } );
} );


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













<table id="example" class="display" width="100%"></table>





<div class="container-fluid">
    <br><br>

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

        </tbody>
    </table>
    
</div>

<?php include "04_footer.php"; ?>
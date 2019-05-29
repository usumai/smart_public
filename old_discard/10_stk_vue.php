<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<script src="includes/vue.js"></script>
<script src="includes/vuetify.js"></script>


<br><br><br><br>
<!-- <ul id="example-1">
  <li v-for="asset in assets">
    {{ asset.assetid }}
  </li>
</ul> -->
<script type="text/javascript">
$(document).ready(function() {
    $('#tbl_assets').DataTable({
        stateSave: true
    });
});
</script>

<div class="container" id="vue_main">
    <table id="tbl_assets" class="table table-sm" width="100%">
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
            <tr v-for="ass in assets">
                <td><a href='' class=''></a>{{ ass.Asset }}</td>
                <td>{{ ass.Asset }}<br><small>{{ ass.Class }}</small></td>
                <td>{{ ass.Location }}<br><small>{{ ass.Room }}</small></td>
                <td>{{ ass.AssetDesc1 }}<br><small>{{ ass.AssetDesc2 }}</small></td>
                <td>{{ ass.InventNo }}<br><small>{{ ass.SNo }}</small></td>
                <td>{{ ass.impairment_code }}</td>
                <td>{{ ass.CurrentNBV }}</td>
                <td><a href='' class=''></a>{{ ass.Asset }}</td>
                <td>{{ ass.ass_id }}
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" name="hello"  v-on:click="changed">Action</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>






<script>
var vue_main = new Vue({
  el: '#vue_main',
  data: {
    assets: [
        <?php
        $sql = "SELECT ass_id, Asset, first_found_flag, Subnumber, Class, Location, Room, AssetDesc1, AssetDesc2, InventNo, SNo, impairment_code, CurrentNBV, res_reason_code, res_completed FROM smartdb.sm14_ass WHERE stkm_id IN (SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND delete_date IS NULL) LIMIT 10000;";
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
                echo "{ass_id:'".$ass_id."',Asset:'".$Asset."',first_found_flag:'".$first_found_flag."',Subnumber:'".$Subnumber."',Class:'".$Class."',Location:'".$Location."',Room:'".$Room."',AssetDesc1:'".$AssetDesc1."',AssetDesc2:'".$AssetDesc2."',InventNo:'".$InventNo."',SNo:'".$SNo."',impairment_code:'".$impairment_code."',CurrentNBV:'".$CurrentNBV."',res_reason_code:'".$res_reason_code."',res_completed:'".$res_completed."'},";
        }}
        ?>
    ]
  },
  methods: {
    changed: function() {
      // console.log(this.names);
      console.log("Party");

    }
  }
})
</script>


<?php include "04_footer.php"; ?>
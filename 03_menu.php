<?php
$smartm_software_version = 0.1;
$icon_tick = "<i class='far fa-check-circle'></i>";
$drpd_div = "<div class='dropdown-divider'></div>";

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$btn_push_master = "";
if (strpos($actual_link, "110_smarter_master")){
    $btn_push_master = $drpd_div."<button type='button' class='dropdown-item btn btn-danger' data-toggle='modal' data-target='#modal_confirm_push'>Push to master</button>";
}




$test_internet = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
if ($test_internet){
    $internet_connectivity = true; //action when connected

    $URL = 'https://raw.githubusercontent.com/usumai/smart_public/master/08_version.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data, true);
    $latest_version_no      = $json["latest_version_no"];
    $version_publish_date   = $json["version_publish_date"];

    $sql_save = "UPDATE smartdb.sm10_set SET date_last_update_check=NOW(); ";
    mysqli_multi_query($con,$sql_save);

}else{
    $internet_connectivity = false; //action in connection failure
    $latest_version_no      = null;
    $version_publish_date   = null;
}

$sql = "SELECT * FROM smartdb.sm10_set;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $date_last_update_check    = $row["date_last_update_check"];
        $smartm_software_version    = $row["smartm_software_version"];
}}
if (empty($date_last_update_check)) {
    $date_last_update_check = "Never!";
}
$area_version_status    = "<span class='dropdown-item'>Up to date as of $version_publish_date</span>";
$area_last_update       = "<h6 class='dropdown-header'>Last checked for updates: $date_last_update_check</h6>";

$sql = "SELECT COUNT(*) as livecount FROM smartdb.sm13_stk WHERE stk_include = 1;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $livecount            = $row["livecount"];
}}
if($livecount>0){
    // $btn_stk = "<a href='10_stk.php' class='nav-link btn btn-sm btn-success'>Stocktake</a>";
    $btn_stk  = "<a href='10_stk.php' class='nav-link text-success'>Stocktake</a>";
    $btn_ff   = "<a href='12_ff.php' class='nav-link text-info'>Add First Found</a>";
    if ($latest_version_no>$smartm_software_version) {
        $area_version_status = "<span class='dropdown-item'>You cannot update software when you have open stocktakes</span>";
    }
}else{
    $btn_stk  = "<span class='nav-link text-secondary' >Stocktake</span>";
    $btn_ff   = "<span class='nav-link text-secondary'>Add First Found</span>";

    if ($latest_version_no>$smartm_software_version) {
        $area_version_status = "<button type='button' class='dropdown-item btn btn-danger' data-toggle='modal' data-target='#modal_confirm_update'>Update to v$latest_version_no</button>";
    }
}

if (!$internet_connectivity) {
    $area_version_status    = "<span class='dropdown-item'>An internet connection is required to update software</span>";
}


$sql = "SELECT count(*) as rowcount_rr FROM smartdb.sm12_rwr;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rowcount_rr = $row["rowcount_rr"];
}}
if ($rowcount_rr>0) {
    $status_rr = "<span class='dropdown-item'>$rowcount_rr assets</span>";
}else{
    $status_rr = "<span class='dropdown-item'>Not loaded</span>";
}
$area_rr = $drpd_div."<h6 class='dropdown-header'>Raw remainder</h6>".$status_rr;
                        

$menu_software = $area_last_update . $area_version_status.$btn_push_master ;


?>

<script>
$( function() {
    $( "#tags" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: "05_action.php",
                data: {
                    act: "get_asset_list",
                    search_term: request.term
                },
                success: function( data ) {
                    console.log(data);
                    json = JSON.parse(data)

                    response(json);
                    console.log(json)
                }
            });
        },
        select: function( event, ui ) {
            // console.log("Selected: " + ui.item.value + " aka " + ui.item.id )
            window.location.href = "11_ass.php?ass_id="+ui.item.value;
        }
    })

               // $arr["AssetDesc1"]       = $row["AssetDesc1"];
               // $arr["AssetDesc2"]       = $row["AssetDesc2"];
               // $arr["InventNo"]         = $row["InventNo"];
               // $arr["SNo"]              = $row["SNo"];
               // $arr["Location"]         = $row["Location"];
               // $arr["Room"]             = $row["Room"];
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( 
            "<div><b>"+item.Asset+"-"+item.Subnumber+"</b>:"+item.AssetDesc1+
            "<br>"+item.status_compl+" InventNo["+item.InventNo+"] Serial["+item.SNo+"] Location["+item.Location+""+item.Room+"]</div>" )
        .appendTo( ul );
    };

});
</script>


<body class="d-flex flex-column h-100">
<header>
  <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="index.php">smartM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><?=$btn_stk?></li>
                <li class="nav-item"><?=$btn_ff?></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">v<?=$smartm_software_version?></a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01"><?=$menu_software?></div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Help</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class='dropdown-item' href='05_action.php?act=sys_open_image_folder'>Image folder</a>
                        <a class='dropdown-item' href='06_admin.php'>Archived Stocktakes</a>
                        <button type='button' class='dropdown-item btn btn-danger' data-toggle='modal' data-target='#modal_confirm_reset'>Reset all data</button>
                        <a class="dropdown-item" href="05_action.php?act=save_invertcolors">Invert Colour Scheme</a>
                        <?=$area_rr?>
                    </div>
                </li>
                <li class="nav-item">
                    <div class="ui-widget">
                        <input id="tags" class='form-control'>
                    </div>
                </li>
            </ul>
            <?=$btn_stk?>





        </div>



    </nav>
</header>


<!-- <br><br><br> -->


<!-- Modal -->
<div class="modal fade" id="modal_confirm_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update to latest version</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="lead">Updating to the latest version will delete all data on this device. Are you Sure you want to proceed with the update?<br><br>Please keep device connected to the internet until the update is finished.</p>     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_pull_master'>Update</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_confirm_push" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Push this version to the master</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
        <p class="lead">This will overwrite the existing master file. Only do this if you are a guru developer.<br><br>Please keep device connected to the internet until the update is finished.</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_push_master'>Update</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_confirm_reset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete all data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
        <p class="lead">Reseting SMARTm will delete all data on this device.<br><br>Are you sure you want to proceed?</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_reset_data'>Reset</a>
      </div>
    </div>
  </div>
</div>
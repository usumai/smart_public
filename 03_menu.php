<?php
$smartm_software_version = 0.1;
$icon_tick = "<i class='far fa-check-circle'></i>";
$drpd_div = "<div class='dropdown-divider'></div>";

$developer=false;
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($actual_link, "110_smarter_master")){
    $developer=true;
}


$test_internet = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
if ($test_internet){
    $internet_connectivity = true; //action when connected
//     ini_set("allow_url_fopen", 1);
//     file_get_contents("test.txt");
//     $jsonData = json_decode(file_get_contents('https://chart.googleapis.com/chart?cht=p3&chs=250x100&chd=t:60,40&chl=Hello|World&chof=json'));
//     $json = file_get_contents('https://raw.githubusercontent.com/usumai/smart_public/master/08_version.php', true);
    $site="http://www.google.com";
    $site="https://raw.githubusercontent.com/usumai/smart_public/master/08_version.json";
// $content = file_get_content($site);
// echo $content;

    $ch = curl_init();    
    curl_setopt($ch,CURLOPT_URL,$site);
    $data = curl_exec($ch);
    curl_close($ch);

    echo "<br><br><br>[".$data."]";
    // echo get_remote_data('http://example.com');
    // $obj = json_decode($json);
    // print_r($obj);
    // $latest_version_no      = $obj->latest_version_no;
    // $version_publish_date   = $obj->version_publish_date;
    fclose($test_internet);
}else{
    $internet_connectivity = false; //action in connection failure
}






$sql = "SELECT * FROM smartdb.sm10_set;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $date_last_update_check    = $row["date_last_update_check"];
        $smartm_software_version    = $row["smartm_software_version"];
}}
if (empty($date_last_update_check)) {
    $date_last_update_check = $version_publish_date;
}

$btn_pull_master    = "";
$area_last_update   = "<h6 class='dropdown-header'>Last checked for updates: $date_last_update_check</h6>";
if ($latest_version_no==$smartm_software_version) {
    $area_version_status    = "<span class='dropdown-item'>Up to date as of $version_publish_date</span>";
}else{
    // $btn_pull_master    = "<span class='dropdown-item'>Update to new version:v$latest_smartm_software_version</span>";
    $btn_pull_master        = "<a class='dropdown-item' href='05_action.php?act=sys_pull_master'>Update to new version: v$latest_version_no</a>";
}
$btn_check_updates          = "<a class='dropdown-item' href='05_action.php?act=sys_check_updates'>Check for updates</a>";
$btn_push_master            = $drpd_div."<a class='dropdown-item' href='05_action.php?act=sys_push_master'>Push to master</a>";

if (!$internet_connectivity) {
    $btn_check_updates  = "<span class='dropdown-item'>An internet connection is required to check for updates</span>";
    $btn_pull_master    = "<span class='dropdown-item'>An internet connection is required to update software</span>";
    $btn_push_master    = "<span class='dropdown-item'>An internet connection is required to push master</span>";
}



if ($developer) {//User is accessing the source code - they are a developer

}else{// User is a client - hide developer options
    $btn_push_master            = "";
}

$menu_software = $area_last_update . $btn_check_updates.$btn_pull_master.$btn_push_master ;

$rw_stk = "";
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
}else{
    $btn_stk  = "<span class='nav-link text-secondary' >Stocktake</span>";
    $btn_ff   = "<span class='nav-link text-secondary'>Add First Found</span>";
}

$btn_archive = ""



?>

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
                <li class="nav-item"><a class="nav-link" href="05_action.php?act=sys_initialise">Initialise</a></li>
                <li class="nav-item"><?=$btn_ff?></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">v<?=$smartm_software_version?></a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01"><?=$menu_software?></div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Help</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class='dropdown-item' href='06_admin.php'>Archived Stocktakes</a>
                    </div>
                </li>
            </ul>
        </div>



    </nav>
</header>
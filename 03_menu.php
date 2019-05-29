<?php
$version_no = "0.1";
$icon_tick = "<i class='far fa-check-circle'></i>";


$developer=false;
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($actual_link, "110_smarter_master")){
    $developer=true;
}
$area_version_status        = "<span class='dropdown-item'>Current version as at 2019-05-15: v0.3</span>";
$btn_check_updates          = "<a class='dropdown-item' href='05_action.php?act=sys_check_updates'>Check for updates</a>";
$btn_pull_master            = "<a class='dropdown-item' href='05_action.php?act=sys_pull_master'>Update to latest</a>";
$btn_push_master            = "<a class='dropdown-item' href='05_action.php?act=sys_push_master'>Push to master</a>";
if ($developer) {//User is accessing the source code - they are a developer

}else{// User is a client - hide developer options
    $btn_push_master            = "";
}

$menu_software = $area_version_status.$btn_check_updates.$btn_pull_master.$btn_push_master ;

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
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">v<?=$version_no?></a>
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
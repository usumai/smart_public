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

?>

<body class="d-flex flex-column h-100">
<header>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="#">smartM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="10_stk.php">Stocktake</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="05_action.php?act=sys_initialise">Initialise</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li> -->
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">v<?=$version_no?></a>
        <div class="dropdown-menu" aria-labelledby="dropdown01">
          <?=$menu_software?>
        </div>
      </li>
      </ul>
    </div>
  </nav>
</header>
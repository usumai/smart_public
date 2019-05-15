<?php



$version_no = "0.1";









// Push this version to the branch
// Merge branch

// Branch list
// Master - What users are using
// Development - What I'm working on

// What actions a user can do:
// Check for updates
// Update application - only if internet exists and stocktake is not half completed

// What actions a developer can do
// Push current local build to development
// Merge development into master (With password)

$icon_tick = "<i class='far fa-check-circle'></i>";

$developer=false;
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($actual_link, "110_smarter_master")){
    $developer=true;
}
$area_version_status        = "<span class='dropdown-item'>Current version as at 2019-05-15: v0.3</span>";
$btn_check_updates          = "<a class='dropdown-item' href='05_action.php?act=sys_check_updates'>Check for updates</a>";
$btn_pull_master            = "<a class='dropdown-item' href='05_action.php?act=sys_pull_master'>Update to latest</a>";
$btn_pull_development       = "<a class='dropdown-item' href='05_action.php?act=sys_pull_development'>Pull development branch</a>";
$btn_push_development       = "<a class='dropdown-item' href='05_action.php?act=sys_push_development'>Push to development branch</a>";
$btn_merge_dev_to_master    = "<a class='dropdown-item' href='05_action.php?act=sys_merge_dev_to_master'>Merge dev into master</a>";
if ($developer) {//User is accessing the source code - they are a developer

}else{// User is a client - hide developer options
    $btn_pull_development       = "";
    $btn_push_development       = "";
    $btn_merge_dev_to_master    = "";
}

$menu_software = $area_version_status.$btn_check_updates.$btn_pull_master.$btn_pull_development.$btn_push_development.$btn_merge_dev_to_master ;

?>

<!doctype html>
<html lang="en" class="h-100">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<title>SMARTm</title>
		<link href="includes/bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="includes/octicons/octicons.min.css">
        <link href="includes/fontawesome-free-5.8.2-web/css/all.css" rel="stylesheet"> <!--load all styles -->
        <link rel="stylesheet" href="a_includes/jquery-ui.css">
        <script type="text/javascript" language="javascript" src="a_includes/jquery-3.3.1.js"></script>
        <script src="a_includes/jquery-ui.js"></script>
        <script src="a_includes/popper.min.js"></script>
	</head>


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
          <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
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

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  <div class="container">
    <h1 class="mt-5">Sticky footer with fixed navbar</h1>
    <p class="lead">Pin a footer to the bottom of the viewport in desktop browsers with this custom HTML and CSS. A fixed navbar has been added with <code>padding-top: 60px;</code> on the <code>main &gt; .container</code>.</p>
    <p>Back to <a href="/docs/4.3/examples/sticky-footer/">the default sticky footer</a> minus the navbar.</p>
  </div>
</main>

<script type="text/javascript">
	// alert();
</script>


<script src="includes/jquery-3.4.1.min.js"></script>
<script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



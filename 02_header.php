<?php

$sql = "SELECT * FROM ".$dbname.".smart_l01_settings";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $theme_type    = $row["theme_type"];
}}

if ($theme_type==1) {
    $scheme_color = "
    body{
        background-color: #282923;
        color: white;
    }

    .card{
        background-color: #282923;
        border-color: white;
        color: white;
    }
    .text-muted{
        color: coral!important;
    }
    .form-control{
        background-color: #282923!important;
        color: white!important;
    }
    select{
        background-color: #282923;
        color: white!important;
    }
    b{
        color: coral!important;
    }
    ";
}
// $scheme_color = "";

?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<title>SMART m</title>
		<link href="includes/bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="includes/octicons/octicons.min.css">
        <link href="includes/fontawesome-free-5.8.2-web/css/all.css" rel="stylesheet"> <!--load all styles -->
        <link rel="stylesheet" href="a_includes/jquery-ui.css">
        <script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>
        <script src="includes/jquery-3.4.1.min.js"></script>
        <script src="a_includes/jquery-ui.js"></script>   
        <style type="text/css">
        body {
            padding-top: 1rem;
            overflow-y: scroll;
        }
        <?=$scheme_color?>

    </style>
	</head>
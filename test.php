<?php

$URL = 'https://raw.githubusercontent.com/usumai/smart_public/master/08_version.json';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$data = curl_exec($ch);
curl_close($ch);
$json_decode = json_decode($data, true);
$latest_version_no	 	= $json["latest_version_no"];
$version_publish_date	= $json["version_publish_date"];
?>
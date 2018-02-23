<?php

$path = '/var/www/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);


require_once 'Net/Wifi.php';
$wifi = new Net_Wifi();
//get all wireless interfaces
$interfaces = $wifi->getSupportedInterfaces();
if (count($interfaces) == 0) {
    exit();
}

$networks = $wifi->scan($interfaces[0]);
if (count($networks) == 0) {
    exit();
}

$nets['networks'] = array();

foreach($networks as $net)
{
	array_push($nets['networks'],addslashes($net->ssid));
}

echo json_encode($nets);


?>

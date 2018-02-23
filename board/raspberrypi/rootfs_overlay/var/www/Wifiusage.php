<html>
<?php

$path = '/var/www/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);


require_once 'Net/Wifi.php';
$wifi = new Net_Wifi();
//get all wireless interfaces
$interfaces = $wifi->getSupportedInterfaces();
if (count($interfaces) == 0) {
    echo 'No wireless interfaces found!' . "<br>";
    exit();
}

$networks = $wifi->scan($interfaces[0]);
if (count($networks) == 0) {
    echo 'No wireless networks available.' . "<br>";
    exit();
}


echo "<br>";
if ($wifi->getCurrentConfig($interfaces[0])->associated == true) {
	echo "Connected to \"" . $wifi->getCurrentConfig($interfaces[0])->ssid . "\".<br><br>";
}

echo "<h1>Available Networks:</h1>";
foreach($networks as $net)
{
	echo $net->ssid . "<br>";
}

?>
</html>

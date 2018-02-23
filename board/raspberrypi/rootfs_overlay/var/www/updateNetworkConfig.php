<?php

$eth0DHCP = isset($_POST['eth0DHCP']) ? $_POST['eth0DHCP'] : null;
$eth0IP = isset($_POST['eth0IP']) ? $_POST['eth0IP'] : null;
$eth0Mask = isset($_POST['eth0Mask']) ? $_POST['eth0Mask'] : null;
$eth0Gateway = isset($_POST['eth0Gateway']) ? $_POST['eth0Gateway'] : null;

$wlan0DHCP = isset($_POST['wlan0DHCP']) ? $_POST['wlan0DHCP'] : null;
$wlan0IP = isset($_POST['wlan0IP']) ? $_POST['wlan0IP'] : null;
$wlan0Mask = isset($_POST['wlan0Mask']) ? $_POST['wlan0Mask'] : null;
$wlan0Gateway = isset($_POST['wlan0Gateway']) ? $_POST['wlan0Gateway'] : null;
$wlan0SSID = isset($_POST['wlan0SSID']) ? $_POST['wlan0SSID'] : null;
$wlan0PSK = isset($_POST['wlan0PSK']) ? $_POST['wlan0PSK'] : null;

$request = "sudo /bin/rm /mnt/data/config/interfaces";
exec($request);

$request = "sudo /bin/touch /mnt/data/config/interfaces";
exec($request);

$request = "sudo /bin/chmod 777 /mnt/data/config/interfaces";
exec($request);

$myfile = fopen("/mnt/data/config/interfaces", "w");

if($eth0DHCP == "true")
{
	fwrite($myfile, "auto eth0\niface eth0 inet dhcp\n\n");
}else{
	fwrite($myfile, "auto eth0\n");
	fwrite($myfile, "iface eth0 inet static\n");
	fwrite($myfile, "address ".$eth0IP."\n");
	fwrite($myfile, "netmask ".$eth0Mask."\n");
	fwrite($myfile, "gateway ".$eth0Gateway."\n");
	fwrite($myfile, "\n");
}

if($wlan0DHCP == "true")
{
	fwrite($myfile, "auto wlan0\niface wlan0 inet dhcp\n");
	fwrite($myfile, "pre-up wpa_supplicant -i wlan0 -c /mnt/data/config/wpa_supplicant.conf -B\n\n");
}else{
	fwrite($myfile, "auto wlan0\n");
	fwrite($myfile, "iface wlan0 inet static\n");
	fwrite($myfile, "address ".$wlan0IP."\n");
	fwrite($myfile, "netmask ".$wlan0Mask."\n");
	fwrite($myfile, "gateway ".$wlan0Gateway."\n");
	fwrite($myfile, "pre-up wpa_supplicant -i wlan0 -c /mnt/data/config/wpa_supplicant.conf -B\n");
	fwrite($myfile, "\n");
}

fclose($myfile);

$request = "sudo /bin/chmod 744 /mnt/data/config/interfaces";
exec($request);



$request = "sudo /bin/rm /mnt/data/config/wpa_supplicant.conf";
exec($request);

$request = "sudo /bin/touch /mnt/data/config/wpa_supplicant.conf";
exec($request);

$request = "sudo /bin/chmod 777 /mnt/data/config/wpa_supplicant.conf";
exec($request);

$myfile = fopen("/mnt/data/config/wpa_supplicant.conf", "w");

fwrite($myfile, "ctrl_interface=/var/run/wpa_supplicant\n");
fwrite($myfile, "ap_scan=1\n");
fwrite($myfile, "\n");
fwrite($myfile, "network={\n");
fwrite($myfile, "ssid=\"".$wlan0SSID."\"\n");
fwrite($myfile, "psk=\"".$wlan0PSK."\"\n");
fwrite($myfile, "}\n");

fclose($myfile);

$request = "sudo /bin/chmod 744 /mnt/data/config/wpa_supplicant.conf";
exec($request);

exec("sudo /usr/bin/killall wpa_supplicant");
exec("sudo /sbin/ifdown -f wlan0");
exec("sudo /sbin/ifdown -f eth0");
exec("sudo /sbin/ifup wlan0");
exec("sudo /sbin/ifup wlan0");
exec("sudo /sbin/ifup eth0");

?>

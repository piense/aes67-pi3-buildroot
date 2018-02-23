<?php

//TODO generalize this file to work with a list of interfaces so this can support more than Pi3s

$returnData = array();
//TODO generalize path with 'which'
exec("/sbin/ifconfig eth0", $returnData);

$network['eth0IP'] = "x.x.x.x";
$network['eth0Gateway'] = "x.x.x.x";
$network['eth0Mask'] = "x.x.x.x";
$network['eth0Online'] = false;

$match = array();

foreach($returnData as $line){
	if(preg_match("/^\s*inet addr:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*Bcast:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*Mask:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})/",$line,$match) == 1){
		$network['eth0IP'] = $match[1];
		$network['eth0Mask'] = $match[3];
	}
	if(strpos($line,"RUNNING") !== false)
	{
		$network['eth0Online'] = true;
	}
}

$returnData = array();

//TODO generalize path with 'which'
exec("/sbin/route", $returnData);

foreach($returnData as $line){
	if(preg_match("/^default\s*([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*[0-9.a-zA-Z\s]*eth0$/",$line,$match) == 1){
		$network['eth0Gateway'] = $match[1];
		break;
	}
}

$returnData = array();

exec("/sbin/ifconfig wlan0", $returnData);

$network['wlan0IP'] = "x.x.x.x";
$network['wlan0Gateway'] = "x.x.x.x";
$network['wlan0Mask'] = "x.x.x.x";

$match = array();

foreach($returnData as $line){
	if(preg_match("/^\s*inet addr:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*Bcast:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*Mask:([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})/",$line,$match) == 1){
		$network['wlan0IP'] = $match[1];
		$network['wlan0Mask'] = $match[3];
	}
	if(strpos($line,"RUNNING") !== false)
		$network['wlan0Online'] = true;
}

$returnData = array();

//TODO generalize path with 'which'
exec("/sbin/route", $returnData);

foreach($returnData as $line){
	if(preg_match("/^default\s*([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})\s*[0-9.a-zA-Z\s]*wlan0$/",$line,$match) == 1){
		$network['wlan0Gateway'] = $match[1];
		break;
	}
}

$returnData = array();

exec("/sbin/iwconfig wlan0", $returnData);

$network['wlan0ESSID'] = "";

$match = array();

foreach($returnData as $line){
	if(preg_match("/ESSID:\"(.*)\"$/",$line,$match) == 1){
		$network['wlan0ESSID'] = $match[1];
		break;
	}
}

echo json_encode($network);

?>
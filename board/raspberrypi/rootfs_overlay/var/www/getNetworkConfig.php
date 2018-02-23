<?php
$iface = "";
$match = array();
$handle = fopen("/mnt/data/config/interfaces", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
		
		if(preg_match("/^iface\s*([a-zA-Z0-9]*)\sinet\s*(static|dhcp)$/",$line,$match) == 1){
			$iface = $match[1];
			$networkConfig[$iface.'IP'] = "x.x.x.x";
			$networkConfig[$iface.'Mask'] = "x.x.x.x";
			$networkConfig[$iface.'Gateway'] = "x.x.x.x";
			if($match[2] == "static")
				$networkConfig[$iface."DHCP"] = false;
			else
				$networkConfig[$iface."DHCP"] = true;
		}
		
		if(preg_match("/^\s*$/",$line,$match) == 1){
			$iface = "";
		}
		
		if(preg_match("/^address\s*([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})$/",$line,$match) == 1){
			$networkConfig[$iface.'IP'] = $match[1];
		}
		
		if(preg_match("/^netmask\s*([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})$/",$line,$match) == 1){
			$networkConfig[$iface.'Mask'] = $match[1];
		}
		
		if(preg_match("/^gateway\s*([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})$/",$line,$match) == 1){
			$networkConfig[$iface.'Gateway'] = $match[1];
		}
    }

    fclose($handle);
} else {
    // error opening the file.
}

$networks = array();
$network; //Shouldn't use this one but just in case the file is formatted wrong
$handle = fopen("/mnt/data/config/wpa_supplicant.conf", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {

		if(strpos($line,"{") !== false)
			unset($network);
		
		if(preg_match("/^\s*([a-zA-Z_0-9]*)=(.*)$/",$line,$match) == 1){
			$property = $match[1];
			$network[$property] = $match[2];
			if(preg_match("/^\"(.*)\"$/",$match[2],$match) == 1){
				$network[$property] = $match[1];
			}
		}
		
		if(strpos($line,"}") !== false)
			array_push($networks,$network);
    }

    fclose($handle);
} else {
    // error opening the file.
}

$networkConfig['networks']=$networks;

echo json_encode($networkConfig);

?>
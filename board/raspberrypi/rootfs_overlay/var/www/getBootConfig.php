<?php

$request = "sudo /bin/mount /dev/mmcblk0p1 /mnt/boot";
exec($request);

$HideSplash = false;
$HideRaspberries = false;
$Quiet = false;

$handle = fopen("/mnt/boot/cmdline.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
		
		if(strpos($line,'logo.nologo') !== false){
			$HideRaspberries = true;
		}
		
		if(strpos($line,"quiet") !== false){
			$Quiet = true;
		}
    }

    fclose($handle);
} else {
    // error opening the file.
}

$handle = fopen("/mnt/boot/config.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
		
		if(preg_match("/disable_splash/",$line,$match) == 1){
			$HideSplash = true;
		}
		
    }

    fclose($handle);
} else {
    // error opening the file.
}

$settings['HideSplash'] = $HideSplash;
$settings['HideRaspberries'] = $HideRaspberries;
$settings['Quiet'] = $Quiet;

echo json_encode($settings);

$request = "sudo /bin/umount /dev/mmcblk0p1 /mnt/boot";
exec($request);

?>

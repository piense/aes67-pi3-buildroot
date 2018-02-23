<?php

$SplashScreen = isset($_POST['splash']) ? $_POST['splash'] : null;
$Quiet = isset($_POST['quiet']) ? $_POST['quiet'] : null;
$Raspberries = isset($_POST['raspberries']) ? $_POST['raspberries'] : null;

$request = "sudo /bin/mount /dev/mmcblk0p1 /mnt/boot";
exec($request);

$request = "sudo /bin/rm /usr/cmdline.txt";
exec($request);

$request = "sudo /bin/touch /usr/cmdline.txt";
exec($request);

$request = "sudo /bin/chmod 777 /usr/cmdline.txt";
exec($request);

$myfile = fopen("/usr/cmdline.txt", "w");

fwrite($myfile, "root=/dev/mmcblk0p2 rootwait console=tty1 console=ttyAMA0,115200");

if($Quiet == "true")
{
	fwrite($myfile, " quiet");
}else{ }

if($Raspberries == "true")
{
	fwrite($myfile, " logo.nologo");
}else{ }

fwrite ($myfile, "\n\n");

fclose($myfile);

$request = "sudo /bin/chmod 744 /usr/cmdline.txt";
exec($request);

$request = "sudo /bin/cp /usr/cmdline.txt /mnt/boot/cmdline.txt";
exec($request);

$request = "sudo rm /usr/cmdline.txt";
exec($request);




$request = "sudo /bin/rm /usr/config.txt";
exec($request);

$request = "sudo /bin/touch /usr/config.txt";
exec($request);

$request = "sudo /bin/chmod 777 /usr/config.txt";
exec($request);

$myfile = fopen("/usr/config.txt", "w");

fwrite($myfile, "kernel=zImage\n");
fwrite($myfile, "disable_overscan=1\n");
fwrite($myfile, "gpu_mem_256=100\n");
fwrite($myfile, "gpu_mem_512=100\n");
fwrite($myfile, "gpu_mem_1024=100\n");
fwrite($myfile, "dtoverlay=pi3-miniuart-bt\n");

if($SplashScreen == "true")
{
	fwrite($myfile, "disable_splash=1\n");
}else{
	
}

fwrite($myfile, "\n");

fclose($myfile);

$request = "sudo /bin/chmod 744 /usr/config.txt";
exec($request);

$request = "sudo /bin/cp /usr/config.txt /mnt/boot/config.txt";
exec($request);

$request = "sudo rm /usr/config.txt";
exec($request);

$request = "sudo /bin/umount /mnt/boot";
exec($request);

?>

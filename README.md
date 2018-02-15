An AES67 audio mixer for the Pi3 using the RT kernel patches.

Status: the code is very experimental, should be able to do loopback testing with a compatible AES67 device (tested with a Yamaha Rio).


Other Notes:
Should be buildroot version 2017.02 which would be https://buildroot.org/downloads/buildroot-2017.02.tar.gz Don't use the raspberrypi3_defconfig. If you copy this on top of the buildroot directory it will overwrite the root .config file and just be ready for a 'make'. Then dd the sdcard.img file onto the card dev file. On the first boot it will create a persistent data partition at /mnt/data that I use for development.

From there I was scping the aes binary across to the data partition. The image comes with OpenSSH and a default password of 'MoreCowbell'. Lately I've been using Eclipse which ties nicely into the workflow of transferring the file across and doing remote debugging. That does involve a bit of tweaking to the buildroot config though to get gdb enabled.

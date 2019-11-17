#!/bin/sh

sgdisk -o /dev/mmcblk0
sleep 2
sgdisk -a 1 -n 1:34:161 -c 1:"nvram" -n 2:162:4257 -c 2:"logo" -n 3:4258:4513 -c 3:"env" \
-n 4:4514:37281 -c 4:"Kernel" -n 5:37282:70049 -c 5:"Kernel2" -n 6:70050:2167201 -c 6:"Rootfs" \
-n 7:2167202:4264353 -c 7:"Rootfs2" -n 8:4264354:`sgdisk -E /dev/mmcblk0` -c 8:"Userfs" /dev/mmcblk0

exit 0

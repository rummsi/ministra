#!/bin/sh

source /etc/init.d/shell-utils.sh

umount /usr/local
ubidetach -p /dev/block/by-name/raw
ubiformat /dev/block/by-name/raw -y
ubiattach -p /dev/block/by-name/raw

get_flash_type /dev/block/by-name/bootloader type
if [ $type == "nand" ]; then
    ubimkvol /dev/ubi0 --lebs=896 --type=dynamic -N Rootfs
    ubimkvol /dev/ubi0 --lebs=896 --type=dynamic -N Rootfs2
    ubimkvol /dev/ubi0 --lebs=44 --type=dynamic -N Userfs
else
    ubimkvol /dev/ubi0 --lebs=896 --type=dynamic -N Rootfs
    ubimkvol /dev/ubi0 --lebs=896 --type=dynamic -N Rootfs2
    ubimkvol /dev/ubi0 --lebs=88 --type=dynamic -N Userfs
fi

exit 0

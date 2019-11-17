#!/bin/sh

# UserFS
userfs_name=Userfs
userfs_mtd=`fdisk -l | grep -i userfs | awk '{print $1}'`
if [ "$userfs_mtd" != "" ] ; then
    umount /mnt/Userfs
    mkfs.ext4 /dev/mmcblk0p${userfs_mtd} 
    mount -t ext4 /dev/mmcblk0p$userfs_mtd /mnt/Userfs
    mkdir /mnt/Userfs/data
fi

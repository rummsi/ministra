#!/bin/sh

# UserFS
userfs_name=Userfs
userfs_numb=`ubinfo /dev/ubi0 -N Userfs | grep "Volume ID:" | awk '{print $3}'`
if [ "$userfs_numb" != "" ] ; then
    umount /mnt/Userfs
    ubiupdatevol /dev/ubi0_${userfs_numb} -t
    mount -t ubifs /dev/ubi0_$userfs_numb /mnt/Userfs
    mkdir -p /mnt/Userfs/data
fi

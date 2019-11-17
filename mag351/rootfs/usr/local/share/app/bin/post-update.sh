#!/bin/sh

TMP_DIR=/tmp/post_update_ext4_resize

fix_ext4() {
        # Remount partition
        sync
        mount -t ext4 /dev/$1 $TMP_DIR  >/dev/null 2>/dev/null
        touch $TMP_DIR/usr/local/share
        sleep 1
        umount $TMP_DIR
        sync
        sleep 1
        # Resize partition
        e2fsck -f -y /dev/$1 >/dev/null 2>/dev/null
        sleep 1
        len=`cat /sys/class/block/$1/size 2>/dev/null`
        len=$((len/2048))
        resize2fs /dev/$1 ${len}M  >/dev/null 2>/dev/null
        if [ "$?" -ne "0" ]; then
            echo "ERROR: Resize $1 failed!!!"
            exit 1
        fi
        sleep 1
}

mkdir -p $TMP_DIR

#echo "post-update.sh $1" > /dev/tty

fix_ext4 $1

echo "OK"

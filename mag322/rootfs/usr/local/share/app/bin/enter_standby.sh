#!/bin/sh

. /etc/init.d/shell-utils.sh

if [ "$1" == "active" ]; then
    echo 3 > /sys/class/leds/mag-front-led/mode
else
    isNfsMount NFS_MOUNT
    if [ "${NFS_MOUNT}" == "n" ]; then
        ifdown eth0
    fi
    ifdown wlan0
    /etc/init.d/wifi_deinit.sh
fi
sync
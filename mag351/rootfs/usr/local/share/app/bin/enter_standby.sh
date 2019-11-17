#!/bin/sh
. /etc/utils/shell-utils.sh

if [ "$1" == "active" ]; then
    echo 3 > /sys/class/leds/mag-front-led/mode
else
    #Hack. Disable full-speed device which connected to root-hub
    echo "0" > /sys/devices/platform/rdb/f0470400.ohci_v2/usb5/authorized

    isNfsMount NFS_MOUNT
    if [ "${NFS_MOUNT}" == "n" ]; then
        ifdown eth0
    fi
    ifdown wlan0
    /etc/init.d/wifi_deinit.sh
    if [ -f /ram/wifi_wa_disabled ]; then
        rm /ram/wifi_wa_disabled;
    fi
fi
sync
#!/bin/sh

. /etc/init.d/shell-utils.sh

start_eth0()
{
    ifup eth0
}

start_wlan0()
{
    if [ -f /ram/wifi_wa_disabled ]; then
        rm /ram/wifi_wa_disabled;
    fi

    /etc/init.d/wifi_init.sh
    ifup wlan0
}

if [ "$1" == "active" ]; then
    echo 2 > /sys/class/leds/mag-front-led/mode
else
    for i in /dev/sd[a-f] ; do blkid -c /ram/blkid.tab $i; done
    rm /ram/ntpdate.started
    killall ntp.sh

    start_wlan0
    isNfsMount NFS_MOUNT
    if [ "${NFS_MOUNT}" == "n" ]; then
        start_eth0 &
    fi
#sleep 5
fi
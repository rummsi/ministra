#!/bin/sh
. /etc/utils/shell-utils.sh

start_eth0()
{
    ifup eth0
}

start_wlan0()
{
    /etc/init.d/wifi_init.sh
    ifup wlan0
}

if [ "$1" == "active" ]; then
    echo 2 > /sys/class/leds/mag-front-led/mode
else
    #Hack. Enable full-speed device which connected to root-hub
    echo "1" > /sys/devices/platform/rdb/f0470400.ohci_v2/usb5/authorized

    #for i in /dev/sd[a-f] ; do blkid -c /ram/blkid.tab $i; done
    rm /ram/ntpdate.started
    killall ntp.sh

    start_wlan0
    isNfsMount NFS_MOUNT
    if [ "${NFS_MOUNT}" == "n" ]; then
        start_eth0 &
    fi

    kill -s SIGVTALRM `cat /var/run/login.pid`
fi


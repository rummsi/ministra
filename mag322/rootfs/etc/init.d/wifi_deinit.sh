#!/bin/sh

. /etc/init.d/shell-utils.sh
DEBUG=0

WIFI_CURR_DRIVER_FILE=/ram/wifi/curr_driver

wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`
log "wifi_driver=$wifi_driver"
if [ -n "$wifi_driver" ]; then
    log "[i] External WIFI dongle active. Deiniting..."
    /etc/init.d/wifi_if_handler.sh stop
    exit 0
fi

vendor=`cat /sys/bus/sdio/devices/mmc0\:0001\:1/vendor 2>/dev/null`
device=`cat /sys/bus/sdio/devices/mmc0\:0001\:1/device 2>/dev/null`

if [ "$vendor" == "0x024c" ] && [ "$device" == "0xf179" ]; then
    echo "Found [RTL8189FC] wifi module (n)"

    /etc/init.d/wifi_stop_wa.sh
    killall wpa_supplicant
    ifconfig wlan0 down
    rmmod 8189fs
    exit 0
elif [ "$vendor" == "0x0271" ] && [ "$device" == "0x0701" ]; then
    echo "Found [QCA9377] wifi module (AC)"

    /etc/init.d/wifi_stop_wa.sh
    killall wpa_supplicant
    ifconfig wlan0 down
    rmmod wlan
    rmmod cfg80211
    rmmod compat
    exit 0
elif [ "$vendor" == "0x024c" ] && [ "$device" == "0xb822" ]; then
    echo "Found [RTL8822B] wifi module (AC)"

    /etc/init.d/wifi_stop_wa.sh
    killall wpa_supplicant
    ifconfig wlan0 down
    rmmod 88x2bs
    exit 0
fi


#!/bin/sh

. /etc/init.d/shell-utils.sh
DEBUG=0

WIFI_CURR_DRIVER_FILE=/ram/wifi/curr_driver
WIFI_HANDLE_LOCK_FILE=/ram/wifi.lock

wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`
log "wifi_driver=$wifi_driver"
if [ -n "$wifi_driver" ]; then
    log "[i] External WIFI dongle active. Aborting embedded WIFI start and init external WIFI"
    /etc/init.d/wifi_if_handler.sh start
    exit 0
fi

export LD_LIBRARY_PATH="/usr/local/lib:$LD_LIBRARY_PATH"

(

/bin/flock -x 555

log "[+] Locking /ram/wifi.lock..."

vendor=`cat /sys/bus/sdio/devices/mmc0\:0001\:1/vendor 2>/dev/null`
device=`cat /sys/bus/sdio/devices/mmc0\:0001\:1/device 2>/dev/null`

if [ "$vendor" == "0x024c" ] && [ "$device" == "0xf179" ]; then
    log "Found [RTL8189FC] wifi module (n)"

    /usr/bin/gpio -s
    echo "4 4 1 7">/proc/sys/kernel/printk
    insmod /usr/lib/modules/8189fs.ko
    ifconfig wlan0 up
    /usr/bin/wpa_supplicant -iwlan0 -Dwext -c/etc/wpa_supplicant/wpa_supplicant.conf -B
    /etc/init.d/wifi_start_wa.sh &
    /bin/flock -u 555
    exit 0
elif [ "$vendor" == "0x0271" ] && [ "$device" == "0x0701" ]; then
    log "Found [QCA9377] wifi module (AC)"

    echo "4 4 1 7">/proc/sys/kernel/printk
    insmod /usr/lib/modules/compat.ko
    insmod /usr/lib/modules/cfg80211.ko
    insmod /usr/lib/modules/wlan.ko
    /usr/bin/wpa_supplicant -iwlan0  -Dnl80211 -c/etc/wpa_supplicant/wpa_supplicant.conf -B
    /etc/init.d/wifi_start_wa.sh &
    /bin/flock -u 555
    exit 0
fi

/bin/flock -u 555
) 555>> $WIFI_HANDLE_LOCK_FILE
log "[+] Unlocking /ram/wifi.lock..."
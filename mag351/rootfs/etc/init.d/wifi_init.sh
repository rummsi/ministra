#!/bin/sh

export LD_LIBRARY_PATH="/usr/local/lib:/usr/local/share/app/lib:/usr/local/n/bin"

# WiFi interface name
WIFI_DRIVERS_PATH="/usr/local/lib/modules"
WIFI_FIRMWARE_PATH="/usr/local/firmware/brcm"
WIFI_NETAPP_PATH="/usr/local/bin"
WIFI_IFACE_NAME="wlan0"
DEV_NAME="mmcblk0boot0"
LEN_BOOT=`cat /sys/class/block/${DEV_NAME}/size 2>/dev/null`
LEN_BOOT=$((LEN_BOOT*512))

_check_wifi_hwaddr()
{
# Load WiFi hardware address
WIFI_HWADDR=`dd if=/dev/${DEV_NAME} bs=1 count=32 skip=$(($LEN_BOOT-192)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
STR_HWADDR="macaddr=${WIFI_HWADDR}"
STR_FIND=`grep -oE "${STR_HWADDR}" $1`
if [ "${STR_HWADDR}" != "${STR_FIND}" ]; then
    echo ">>> Fixed WiFi MAC address ${WIFI_HWADDR}..."
    sed -i -e "s/^macaddr=.*/${STR_HWADDR}/" $1
fi
}


TEST_USB_WIFI=`lsusb | grep 0a5c:bd27 >/dev/null && echo 1`
if [ ! -z $TEST_USB_WIFI ]; then
    echo ">>> Found Broadcom 43569 WiFi chip..."

    if [ -d "${WIFI_DRIVERS_PATH}" ]; then
        echo "Loading 43569 WiFi kernel modules..."
        _check_wifi_hwaddr ${WIFI_FIRMWARE_PATH}/defaults/bcm43569.nvm
        echo "4 4 1 7">/proc/sys/kernel/printk
        insmod ${WIFI_DRIVERS_PATH}/dhd.ko iface_name=${WIFI_IFACE_NAME}
        ${WIFI_NETAPP_PATH}/bcmdl -n ${WIFI_FIRMWARE_PATH}/defaults/bcm43569.nvm ${WIFI_FIRMWARE_PATH}/bcm43569-firmware.bin
        /usr/local/bin/wpa_supplicant -Dnl80211 -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf -B
        /etc/init.d/wifi_start_wa.sh &
    fi
fi

TEST_PCI_WIFI=`lspci | grep 14e4:aa31 >/dev/null && echo 1`
if [ ! -z $TEST_PCI_WIFI ]; then
    echo ">>> Found Broadcom 43570 WiFi chip..."

    if [ -d "${WIFI_DRIVERS_PATH}" ]; then
        echo "Loading 43570 WiFi kernel modules..."
        _check_wifi_hwaddr ${WIFI_FIRMWARE_PATH}/defaults/bcm43570.nvm

        echo "4 4 1 7">/proc/sys/kernel/printk
        /usr/local/n/bin/wait_for_server -timeout 0 2>/dev/null
        if [ "$?" == "0" ] ; then
            /usr/local/bin/BrcmWifiSecDmaHelper /etc/init.d/wifi_bcm43570_insmod.sh

            /usr/local/bin/wpa_supplicant -Dnl80211 -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf -B
            /etc/init.d/wifi_start_wa.sh &
        fi
    fi
fi

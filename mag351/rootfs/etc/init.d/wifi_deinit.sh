#!/bin/sh

TEST_USB_WIFI=`lsusb | grep 0a5c:bd27 >/dev/null && echo 1`
if [ ! -z $TEST_USB_WIFI ]; then
    echo ">>> Remove 43569 WiFi chip..."

    killall wpa_supplicant
    rmmod bcmdhd
fi

TEST_PCI_WIFI=`lspci | grep 14e4:aa31 >/dev/null && echo 1`
if [ ! -z $TEST_PCI_WIFI ]; then
    echo ">>> Remove 43570 chip..."

    /etc/init.d/wifi_stop_wa.sh
    killall wpa_supplicant
    rmmod bcmdhd
    killall BrcmWifiSecDmaHelper
fi



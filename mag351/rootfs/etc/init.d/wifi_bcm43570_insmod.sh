#!/bin/sh
WIFI_DRIVERS_PATH="/usr/local/lib/modules"
WIFI_FIRMWARE_PATH="/usr/local/firmware/brcm"
WIFI_IFACE_NAME="wlan0"

insmod ${WIFI_DRIVERS_PATH}/dhd_pcie.ko iface_name=${WIFI_IFACE_NAME} nvram_path=${WIFI_FIRMWARE_PATH}/defaults/bcm43570.nvm firmware_path=${WIFI_FIRMWARE_PATH}/bcm43570-firmware.bin $*

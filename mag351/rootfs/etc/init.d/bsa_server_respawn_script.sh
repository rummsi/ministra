#!/bin/sh

echo "start bsa_sever"

while true; do

# cd /usr/local/bin/ is necessary
cd /usr/local/bin/

/usr/local/bin/bsa_server -d /dev/btusb0 -all=0 -p /usr/local/firmware/brcm/BCM43569A2_001.003.004.0099.0154_Generic_USB_40MHz_fcbga_BU_WakeOn_BLE_InfoMir.hcd -u /ram/run/ > /dev/null 2>&1
#/usr/local/bin/bsa_server -d /dev/btusb0 -all=0 -p /usr/local/firmware/brcm/BCM43569A2_001.003.004.0013.0000_Generic_USB_40MHz_fcbga_BU_WakeOn_BLE_generic_based_RRAM.hcd -u /ram/run/ > /dev/null 2>&1

echo "restart bsa_sever!!!"

sleep 1

done
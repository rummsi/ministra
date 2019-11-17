#!/bin/sh

. /etc/init.d/shell-utils.sh
DEBUG=0

WIFI_ID_FILE=/ram/wifi/wifi-id.lock
WIFI_CURR_DRIVER_FILE=/ram/wifi/curr_driver
WIFI_HANDLE_LOCK_FILE=/ram/wifi.lock

WIFI_DRIVER_MODULE_NAME="ralink"
WIFI_DRIVER_MT_MODULE_NAME="mt7601"
WIFI_DRIVER_MT7610_MODULE_NAME="mt7610"
WIFI_DRIVER_REALTEK_MODULE_NAME="8188eu"

WPA_SUPPLICANT_BIN=/usr/bin/wpa_supplicant

export LD_LIBRARY_PATH="/usr/local/lib:$LD_LIBRARY_PATH"

restart_work_around()
{
    LLL=`ls -la /ram/wifi_wa_disabled`
    log "restart_work_around::log = $LLL"

    if [ -f /ram/wifi_wa_disabled ]; then
        rm /ram/wifi_wa_disabled;
    fi

    LLL2=`ls -la /ram/wifi_wa_disabled`
    log "restart_work_around::log2 = $LLL2"

    /etc/init.d/wifi_start_wa.sh &
}
unload_embedded_module()
{
    if [ "`cat ${WIFI_CURR_DRIVER_FILE}`" == "" ]; then
        log "[+] Unload driver for embedded WIFI module..."
        /etc/init.d/wifi_deinit.sh
        log "[i] Driver for embedded WIFI module ULOADED"
    else
        log "[i] No need to unload embedded driver cause we have already inited EXTERNAL driver"
    fi
}
stop_supplicant()
{
    log "[+] Going to STOP SUPPLICANT..."
    killall -9 wpa_supplicant

    # stop all interface services
    /etc/init.d/rc.network-updown stop wlan0

    log "[i] SUPPLICANT STOPPED"
}
restart_supplicant()
{
    # stop old instance of SUPPLICANT if existed
    if [ "`ps fax | grep /usr/bin/wpa_supplicant | grep -v grep`" != "" ]; then 
        log "[+] Going to STOP old instance of SUPPLICANT"
        killall -9 wpa_supplicant
        sleep 1
        log "[i] Old instance of SUPPLICANT STOPPED"
    fi

    # stop all interface services
    /etc/init.d/rc.network-updown stop wlan0

    # start SUPPLICANT
    log "[+] Going to START SUPPLICANT..."

    AAA=`ifconfig wlan0`
    log "$AAA"

    #ifconfig wlan0 up
    BBB=`/usr/bin/wpa_supplicant -B -Dwext -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf 2>&1`
    log "$BBB"

    log "[i] SUPPLICANT STARTED"
}
load_module()
{
    log "Load kernel module. wifi_driver='$wifi_driver'"

    if [ "$WIFI_DRIVER_MT_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Loading '$WIFI_DRIVER_MT_MODULE_NAME' modules..."
        insmod /usr/lib/modules/mt7601Usta.ko
        return;
    fi

    if [ "$WIFI_DRIVER_MT7610_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Loading '$WIFI_DRIVER_MT7610_MODULE_NAME' modules..."
        insmod /usr/lib/modules/mt7610u_sta.ko
        return;
    fi

    if [ "$WIFI_DRIVER_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Loading '$WIFI_DRIVER_MODULE_NAME' modules..."
        insmod /usr/lib/modules/rt5370sta.ko
        echo 2001 3c1e >> /sys/bus/usb/drivers/rt2870/new_id
        echo 0b05 17e8 >> /sys/bus/usb/drivers/rt2870/new_id
        echo 0b05 179d >> /sys/bus/usb/drivers/rt2870/new_id
        return;
    fi

    if [ "$WIFI_DRIVER_REALTEK_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Loading '$WIFI_DRIVER_REALTEK_MODULE_NAME' modules..."
        insmod /usr/lib/modules/8188eu.ko
        return;
    fi

    log "Could not load any modules!!! Ignoring load..."
}

unload_module()
{
    #wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`

    log "Unload kernel module for driver '${wifi_driver}'...'"

    /sbin/ifconfig wlan0 down
    sleep 1

    if [ "$WIFI_DRIVER_MT_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Unloading '$WIFI_DRIVER_MT_MODULE_NAME' modules..."
        sleep 1
        rmmod mt7601Usta
        sleep 1
        LLL=`lsmod`
        log "Modules list after unloading: $LLL"
        return;
    fi

    if [ "$WIFI_DRIVER_MT7610_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Unloading '$WIFI_DRIVER_MT7610_MODULE_NAME' modules..."
        rmmod mt7610u_sta
        return;
    fi

    if [ "$WIFI_DRIVER_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Unloading '$WIFI_DRIVER_MODULE_NAME' modules..."
        rmmod rt5370sta
        return;
    fi

    if [ "$WIFI_DRIVER_REALTEK_MODULE_NAME" == "${wifi_driver}" ]; then
        log "Unloading '$WIFI_DRIVER_REALTEK_MODULE_NAME' modules..."
        rmmod $WIFI_DRIVER_REALTEK_MODULE_NAME
        return;
    fi

    log "Could not unload any modules!!! Ignoring unload..."
}

detect_module()
{
    log "Detect module type. USB_PRODUCT='${USB_PRODUCT}', USB_VENDOR_ID='${USB_VENDOR_ID}', USB_PRODUCT_ID='${USB_PRODUCT_ID}'"

  if [ "${USB_VENDOR_ID}" == "0bda" ]; then
    log "found vendor Realtek"
      export wifi_driver=$WIFI_DRIVER_REALTEK_MODULE_NAME
      retVal=1
      return
  fi

  if [ "${USB_VENDOR_ID}" == "148f" ]; then
    log "found vendor = Ralink Technology, Corp."
    if [ "${USB_PRODUCT_ID}" == "7601" ]; then
      log "found product = MediaTek mt7601u"
      export wifi_driver=$WIFI_DRIVER_MT_MODULE_NAME
      retVal=1
      return
    fi
  fi

    if echo "${USB_PRODUCT}" | egrep -i "(802.11)|(WLAN)|(80211)" > /dev/null ; then
        log "Found '802.11n WLAN Adapter' product family device. Trying it..."
        export wifi_driver=${WIFI_DRIVER_MODULE_NAME}
        retVal=1
        return
    fi



  if [ "${USB_VENDOR_ID}" == "0b05" ]; then
    log "found vendor = ASUS(0x0b05)"
    if [ "${USB_PRODUCT_ID}" == "1784" ]; then
      log "found product = USB-N13(0x1784)"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "07d1" ]; then
    log "found vendor = D-Link System (0x07d1)"
    if [ "${USB_PRODUCT_ID}" == "3c0d" ]; then
      log "found product = DWA-125 Wireless 150 USB Adapter"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "07d1" ]; then
    log "found vendor = D-Link System (0x07d1)"
    if [ "${USB_PRODUCT_ID}" == "3c16" ]; then
      log "found product = DWA-125 Wireless N 150 Adapter (rev.A2)"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "2001" ]; then
    log "found vendor = D-Link System (0x07d1)"
    if [ "${USB_PRODUCT_ID}" == "3c19" ]; then
      log "found product = DWA-125 Wireless N 150 Adapter (rev.A3)"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "7392" ]; then
    log "found vendor = Edimax (0x7392)"
    if [ "${USB_PRODUCT_ID}" == "7711" ]; then
      log "found product = EW-7711UAn (0x7711)"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi
  if [ "${USB_VENDOR_ID}" == "148f" ]; then
    log "found vendor = Ralink Technology, Corp. (0x148f)"
    if [ "${USB_PRODUCT_ID}" == "3070" ] || [ "${USB_PRODUCT_ID}" == "5370" ]; then
      log "found product = RT2870/RT3070 Wireless Adapter (0x3070)"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "1737" ]; then
    log "found vendor = Ralink Technology, Corp. (0x148f)"
    if [ "${USB_PRODUCT_ID}" == "0079" ] || [ "${USB_PRODUCT_ID}" == "0079" ]; then
      log "WUSB600N"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if [ "${USB_VENDOR_ID}" == "7392" ]; then
    log "found vendor = Ralink Technology, Corp. (0x148f)"
    if [ "${USB_PRODUCT_ID}" == "7733" ] || [ "${USB_PRODUCT_ID}" == "7733" ]; then
      log "WUSB600N"
      export wifi_driver=$WIFI_DRIVER_MODULE_NAME
      retVal=1
      return
    fi
  fi

  if echo $4 | grep -i "ralink" > /dev/null ; then
    log "found vendor = Ralink Technology, Corp. (common case)"
    export wifi_driver=$WIFI_DRIVER_MODULE_NAME
    retVal=1
    return
  fi

    log "going to test mfct = ${USB_MANUFACTURER}"

    if echo "${USB_MANUFACTURER}" | grep -i "MediaTek" > /dev/null ; then
        log "Found device of 'MediaTek' manufacturer. Trying it..."
        if [ "${USB_PRODUCT_ID}" == "7610" ]; then
            log "Found product family MediaTek/mt7610"
            export wifi_driver=$WIFI_DRIVER_MT7610_MODULE_NAME
            retVal=1
            return
        fi
    fi

    if echo "${USB_MANUFACTURER}" | grep -i "ralink" > /dev/null ; then
        log "Found device of 'Ralink Technology' manufacturer. Trying it..."
        export wifi_driver=$WIFI_DRIVER_MODULE_NAME
        retVal=1
        return
    fi
}


# EXECUTION POINT

log "[i] ENTRY"

log "Action='$1', USB_PRODUCT='${USB_PRODUCT}', DEVPATH='${DEVPATH}'"

export wifi_bus_id=${DEVPATH}

(

log "[+] Locking /ram/wifi.lock..."
/bin/flock -x 555
log "[+] Lock acquired"

case $1 in
    add)
        log "[+] Cmd: ADD."

        if [ -f $WIFI_CURR_DRIVER_FILE ]; then
            log "WIFI is already activated. Exiting..."
            /bin/flock -u 555
            exit 1;
        fi

        detect_module

        if [ "$wifi_driver" == "" ]; then
            log "Unsupported product. Exiting..."
            /bin/flock -u 555
            exit 1;
        fi

        # unload EMBEDDED WIFI driver cause we are going to load EXTERNAL one
        unload_embedded_module

        echo "${wifi_bus_id}" > $WIFI_ID_FILE
        echo "${wifi_driver}" > $WIFI_CURR_DRIVER_FILE

        # load EXTERNAL WIFI driver
        load_module
        log "[i] Load OK"


        # restart SUPPLICANT
        log "[+] Restarting SUPPLICANT"
        restart_supplicant

        # start Work-Around (early setup WIFI connection without application)
        log "[+] Work Around start"
        restart_work_around
    ;;
    remove)
        log "[+] Cmd: REMOVE"

        if [ ! -f $WIFI_CURR_DRIVER_FILE ]; then
            log "WIFI isn't activated. Exiting..."
            /bin/flock -u 555
            exit 1;
        fi
        wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`

        curr_wifi_id_lock=`cat $WIFI_ID_FILE`

        if [ "${wifi_bus_id}" == "$curr_wifi_id_lock" ]; then
            stop_supplicant

            sleep 3

            unload_module

            rm -f $WIFI_ID_FILE
            rm -f $WIFI_CURR_DRIVER_FILE
        else
            log "Its not the WiFi dongle we allowed to deal with"
        fi
    ;;
    stop)
        log "[+] Cmd: STOP"

        if [ ! -f $WIFI_CURR_DRIVER_FILE ]; then
            log "WIFI isn't activated. Exiting..."
            /bin/flock -u 555
            exit 1;
        fi
        wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`

        # stop supplicant
        stop_supplicant

        sleep 3

        # stop driver
        unload_module
    ;;
    start)
        log "[+] Cmd: START"

        if [ ! -f $WIFI_CURR_DRIVER_FILE ]; then
            log "WIFI isn't activated. Exiting..."
            /bin/flock -u 555
            exit 1;
        fi
        wifi_driver=`cat ${WIFI_CURR_DRIVER_FILE}`

        # load driver
        log "[i] Starting WIFI driver..."
        load_module
        log "[+] WIFI driver started OK"

        # restart SUPPLICANT
        restart_supplicant

        # start Work-Around (early setup WIFI connection without application)
        log "[+] Work Around start"
        restart_work_around
    ;;
esac

log "[i] Unlocking /ram/wifi.lock..."
/bin/flock -u 555
) 555>> $WIFI_HANDLE_LOCK_FILE
log "[+] Unlocked /ram/wifi.lock..."


log "DONE."

exit 0

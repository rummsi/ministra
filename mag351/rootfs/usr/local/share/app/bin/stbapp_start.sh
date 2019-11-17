#!/bin/sh
source /etc/utils/shell-utils.sh
cd /usr/local/share/app/; 

# read portal variables
PORTAL_1=`fw_printenv portal1 2>/dev/null`
PORTAL_1=${PORTAL_1#portal1=}
PORTAL_2=`fw_printenv portal2 2>/dev/null`
PORTAL_2=${PORTAL_2#portal2=}
USE_PORTAL_DHCP=`fw_printenv use_portal_dhcp 2>/dev/null`
USE_PORTAL_DHCP=${USE_PORTAL_DHCP#use_portal_dhcp=}

if [ -z "$USE_PORTAL_DHCP" ]; then
    fw_setenv use_portal_dhcp true
    USE_PORTAL_DHCP=true
fi

BYPASS_WAIT=1

if [ ! -f /ram/dhcp_ready ]; then
    if [ "$PORTAL_1" == "" ] && [ "$PORTAL_2" == "" ] && [ "$USE_PORTAL_DHCP" != "true" ] ; then
        log "all portals disabled."
        BYPASS_WAIT=1
    else
        log "some portals is active..."
        read_nvram_var "ipaddr_conf" "STAT_IP"
        read_nvram_var "wifi_int_ip" "wifi_int_ip"
        read_nvram_var "lan_noip" "LAN_NO_IP"

        if [ "$LAN_NO_IP" == "true" ]; then
            if [ "$wifi_int_ip" == "" ] || [ "$wifi_int_ip" == "0.0.0.0" ] ; then
                log "dhcp config found (only WiFi). waiting..."
                BYPASS_WAIT=0
            fi
        else
            if [ "$STAT_IP" == "" ] || [ "$wifi_int_ip" == "" ] || [ "$wifi_int_ip" == "0.0.0.0" ] ; then
                log "dhcp config found (LAN or WiFi). waiting..."
                BYPASS_WAIT=0
            fi
        fi
    fi
fi

if [ "$BYPASS_WAIT" == "0" ] ; then
    i=30;
    while [ "$i" -ne "0" ] ; do
        if [ -f /ram/dhcp_ready ] ; then
            log "/ram/dhcp_ready FOUND"
            NTP_IP=`fw_printenv ntpurl 2>/dev/null`
            NTP_IP=${NTP_IP#ntpurl=}
            NTP_DHCP=`cat /ram/dhcp_ready | grep "ntpserver="`
            if [ "$NTP_IP" != "" ] || [ "$NTP_DHCP" != "" ]; then
                j=0
                while [ $j -lt 6 ] ; do
                    year=`date +%Y`
                    year=$((year))
                    if [ $year -gt 2015 ] ; then
                        break
                    fi
                    sleep 1
                    j=$(($j+1));
                done
            fi
            break;
        fi
        log "not found, sleeping..."
        sleep 1;
        i=$(($i-1));
    done;
fi

/etc/init.d/wifi_stop_wa.sh

SYSLOG_SRV=""
if [ -f "/ram/dhcp_ready" ]; then
    SYSLOG_SRV=`cat /ram/dhcp_ready | grep "syslog_srv="`
    SYSLOG_SRV=${SYSLOG_SRV%% *#*}
    SYSLOG_SRV=${SYSLOG_SRV#syslog_srv=}
fi

if [ "$SYSLOG_SRV" == "" ]; then
    SYSLOG_SRV=`fw_printenv syslog_srv 2>/dev/null`
    SYSLOG_SRV=${SYSLOG_SRV#syslog_srv=}
fi

/usr/local/share/app/bin/check_dhcp_update.sh

PORTAL_TMP=`cat /ram/dhcp_ready | grep "portal_dhcp="`
PORTAL_TMP=${PORTAL_TMP%%#*}
#trim spaces
PORTAL_TMP=`echo $PORTAL_TMP`
PORTAL_TMP=${PORTAL_TMP#portal_dhcp=}

if [ "$USE_PORTAL_DHCP" == "true" ]; then
    PORTAL_DHCP=`fw_printenv portal_dhcp 2>/dev/null`
    PORTAL_DHCP=${PORTAL_DHCP#portal_dhcp=}
    if [ "$PORTAL_DHCP" != "$PORTAL_TMP" ]; then
        fw_setenv portal_dhcp $PORTAL_TMP
    fi
fi

if [ "$SYSLOG_SRV" == "" ]; then
    ./run.sh > /dev/null 2>&1
else
    ./run.sh 2>&1 | logger -t stbapp
fi

sleep 1

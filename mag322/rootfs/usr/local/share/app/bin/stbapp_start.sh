#!/bin/sh

. /etc/init.d/shell-utils.sh

cd /usr/local/share/app/

# read portal variables
get_fw_env "portal1" PORTAL_1
get_fw_env "portal2" PORTAL_2
get_fw_env "use_portal_dhcp" USE_PORTAL_DHCP
if [ -z "$USE_PORTAL_DHCP" ]; then
    set_fw_env "use_portal_dhcp" true
    USE_PORTAL_DHCP=true
fi

BYPASS_WAIT=1

if [ ! -f /ram/dhcp_ready ]; then
    if [ "$PORTAL_1" == "" ] && [ "$PORTAL_2" == "" ] && [ "$USE_PORTAL_DHCP" != "true" ] ; then
        log "all portals disabled."
        BYPASS_WAIT=1
    else
        log "some portals is active..."
        get_fw_env "ipaddr_conf" STAT_IP
        get_fw_env "wifi_int_ip" wifi_int_ip
        get_fw_env "lan_noip" LAN_NO_IP
        get_fw_env "wifi_ssid" WIFI_SSID

        if [ "$LAN_NO_IP" == "true" ] ; then
            if [ "$WIFI_SSID" != "" ] ; then
                if [ "$wifi_int_ip" == "" ] || [ "$wifi_int_ip" == "0.0.0.0" ] ; then
                    log "dhcp config found (only WiFi). waiting..."
                    BYPASS_WAIT=0
                fi
            fi
        else
            if [ "$STAT_IP" == "" ] ; then
                log "dhcp config found (LAN). waiting..."
                BYPASS_WAIT=0
            else
                if [ "$wifi_int_ip" == "" ] || [ "$wifi_int_ip" == "0.0.0.0" ] ; then
                    if [ "$WIFI_SSID" != "" ] ; then
                        log "dhcp config found ( WiFi). waiting..."
                        BYPASS_WAIT=0
                    fi
                fi
            fi
        fi
    fi
fi

if [ "$BYPASS_WAIT" == "0" ] ; then
    i=30;
    while [ "$i" -ne "0" ] ; do
        if [ -f /ram/dhcp_ready ] ; then
            log "/ram/dhcp_ready FOUND"
            break;
        fi
        log "not found, sleeping..."
        sleep 1;
        i=$(($i-1));
    done;
fi

NTP_WAIT_TIME=30
get_fw_env "ntp_wait_time" ntp_to
if [ "$ntp_to" != "" ] ; then
    if [ $ntp_to -ge 0 ] && [ $ntp_to -le 240 ] ; then
      NTP_WAIT_TIME=$ntp_to
    fi
fi

get_fw_env "ntpurl" NTP_IP
NTP_DHCP=`cat /ram/dhcp_ready 2>/dev/null | grep "ntpserver="`
if [ "$NTP_IP" != "" ] || [ "$NTP_DHCP" != "" ]; then
    j=0
    while [ $j -lt $NTP_WAIT_TIME ] ; do
        year=`date +%Y`
        year=$((year))
        if [ $year -gt 2015 ] ; then
            break
        fi
        sleep 1
    j=$(($j+1));
    done
fi

/etc/init.d/wifi_stop_wa.sh

SYSLOG_SRV=""
if [ -f "/ram/dhcp_ready" ]; then
    SYSLOG_SRV=`cat /ram/dhcp_ready | grep "syslog_srv="`
    SYSLOG_SRV=${SYSLOG_SRV%% *#*}
    SYSLOG_SRV=${SYSLOG_SRV#syslog_srv=}
fi

if [ "$SYSLOG_SRV" == "" ]; then
    get_fw_env "syslog_srv" SYSLOG_SRV
fi

/usr/local/share/app/bin/check_dhcp_update.sh

PORTAL_TMP=`cat /ram/dhcp_ready | grep "portal_dhcp="`
PORTAL_TMP=${PORTAL_TMP%%#*}
#trim spaces
PORTAL_TMP=`echo $PORTAL_TMP`
PORTAL_TMP=${PORTAL_TMP#portal_dhcp=}

if [ "$USE_PORTAL_DHCP" == "true" ]; then
    get_fw_env "portal_dhcp" PORTAL_DHCP
    if [ "$PORTAL_DHCP" != "$PORTAL_TMP" ]; then
        set_fw_env "portal_dhcp" $PORTAL_TMP
    fi
fi

if [ "$SYSLOG_SRV" == "" ]; then
    ./run.sh > /dev/null 2>&1
else
    ./run.sh 2>&1 | logger -t stbapp
fi

sleep 1

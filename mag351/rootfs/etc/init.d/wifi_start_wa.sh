#!/bin/sh

get_fw_env() {
    val=`fw_printenv $1 2>/dev/null`
    export $2="${val#$1=}"
}
if [ -f /ram/wifi_wa_disabled ] ; then
    exit 0;
fi
get_fw_env wifi_ssid wifi_ssid
get_fw_env wifi_auth wifi_auth
get_fw_env wifi_off  wifi_off

if [ "$wifi_ssid" == "" ] ; then
    exit 0
fi

if [ "$wifi_off" == "1" ] ; then
    exit 0
fi

wpa_cli disable_network 0
wpa_cli set_network 0 ssid \"$wifi_ssid\"

if [ "$wifi_auth" == "wpapsk" ] || [ "$wifi_auth" == "wpa2psk" ] ; then
    get_fw_env wifi_psk wifi_psk
    wpa_cli set_network 0 psk \"$wifi_psk\"
else
    wpa_cli set_network 0 key_mgmt NONE
    get_fw_env wifi_enc wifi_enc
    if [ "$wifi_enc" == "wep" ] ; then
        get_fw_env wifi_wep_key1 wep_key0
        wpa_cli set_network 0 wep_key0 \"$wep_key0\"
        wpa_cli set_network 0 wep_tx_keyidx 0
    fi
fi

wpa_cli enable_network 0

while [ 1 ] ; do
    status=`wpa_cli status | grep -o "wpa_state=COMPLETED"`
    if [ "$status" != "" ] ; then
        /etc/init.d/rc.network-updown start wlan0
        touch /ram/wifi_wa_disabled
        exit
    fi
    if [ -f /ram/wifi_wa_disabled ] ; then
        exit 0;
    fi
    sleep 1
done
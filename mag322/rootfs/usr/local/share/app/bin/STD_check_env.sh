#!/bin/sh
#

. /etc/init.d/shell-utils.sh

_set_if_absent()
{
    if [ -z $3 ]; then
        fw_args_to_be_executed="$fw_args_to_be_executed$1 $2 | "
    fi
}

# EXECUTION POINT

get_fw_env "tvsystem" tvsystem
get_fw_env "graphicres" graphicres
get_fw_env "language" language
get_fw_env "wifi_ssid" wifi_ssid
get_fw_env "wifi_enc" wifi_enc
get_fw_env "wifi_auth" wifi_auth
get_fw_env "wifi_int_ip" wifi_int_ip
get_fw_env "timezone_conf_int" timezone_conf_int

get_fw_env "dnsip" dnsip
if [ "$dnsip" = "0.0.0.0" ]; then
    set_fw_env dnsip
fi

get_fw_env "wifi_int_dns" wifi_int_dns
if [ "$wifi_int_dns" = "0.0.0.0" ]; then
    set_fw_env wifi_int_dns
fi

fw_args_to_be_executed=""

_set_if_absent "tvsystem" "Auto" $tvsystem
_set_if_absent "graphicres" "1280" $graphicres
_set_if_absent "language" "en" $language
_set_if_absent "wifi_ssid" "default_ssid" $wifi_ssid
_set_if_absent "wifi_auth" "wpa2psk" $wifi_auth
_set_if_absent "wifi_enc" "tkip" $wifi_enc
_set_if_absent "wifi_int_ip" "0.0.0.0" $wifi_int_ip
_set_if_absent "timezone_conf_int" "plus_02_00_13" $timezone_conf_int

# finnaly execute prepared multi fw_setenv
fw_setenv $fw_args_to_be_executed > /dev/null 2>&1

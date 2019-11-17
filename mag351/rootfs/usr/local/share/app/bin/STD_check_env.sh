#!/bin/sh
#

get_nvram_env()
{
    val=`fw_printenv $1 2>/dev/null`
    FW_ENV_RESULT=${val#*=}
}

read_nvram_env()
{
  get_nvram_env $1
  export $1=$FW_ENV_RESULT
}

_set_if_absent()
{
    if [ -z $3 ]; then
	fw_args_to_be_executed="$fw_args_to_be_executed$1 $2 | "
        #fw_setenv $1 "$2"
    fi
}

# EXECUTION POINT

fw_args_to_be_executed=""

read_nvram_env "tvsystem"
read_nvram_env "graphicres"
read_nvram_env "language"
read_nvram_env "wifi_ssid"
read_nvram_env "wifi_enc"
read_nvram_env "wifi_auth"
read_nvram_env "wifi_int_ip"
read_nvram_env "timezone_conf_int"
read_nvram_env "bootstrap_url"
read_nvram_env "update_url"
read_nvram_env "dnsip"
read_nvram_env "wifi_int_dns"

if [ "$dnsip" = "0.0.0.0" ]; then
    fw_setenv dnsip
fi

if [ "$wifi_int_dns" = "0.0.0.0" ]; then
    fw_setenv wifi_int_dns
fi

_set_if_absent "tvsystem" "Auto" $tvsystem
_set_if_absent "graphicres" "1280" $graphicres
_set_if_absent "language" "en" $language
_set_if_absent "wifi_ssid" "default_ssid" $wifi_ssid
_set_if_absent "wifi_auth" "wpa2psk" $wifi_auth
_set_if_absent "wifi_enc" "tkip" $wifi_enc
_set_if_absent "wifi_int_ip" "0.0.0.0" $wifi_int_ip
_set_if_absent "timezone_conf_int" "plus_02_00_13" $timezone_conf_int

# finnaly execute prepared multi fw_setenv
fw_setenv $fw_args_to_be_executed
  
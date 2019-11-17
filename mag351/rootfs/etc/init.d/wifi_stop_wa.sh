#!/bin/sh
get_fw_env() {
    val=`fw_printenv $1 2>/dev/null`
    export $2="${val#$1=}"
}
touch /ram/wifi_wa_disabled
killall wifi_start_wa.sh >/dev/null 2>/dev/null

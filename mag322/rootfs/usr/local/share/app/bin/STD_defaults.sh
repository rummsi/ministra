#!/bin/sh
#

echo "Setting environment to factory defaults..."

fw_setenv ipaddr_conf "|" tvsystem Auto "|" aspect_ratio default "|" language en > /dev/null 2>&1

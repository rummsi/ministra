#!/bin/sh

. /etc/utils/shell-utils.sh

# Boot device name
DEV_NAME="mmcblk0boot0"
DEV_BOOT="/dev/${DEV_NAME}"
LEN_BOOT=`cat /sys/class/block/${DEV_NAME}/size 2>/dev/null`
LEN_BOOT=$((LEN_BOOT*512))
PATH_DRM="/ram/drm"

mkdir -p ${PATH_DRM}

#REVISION=`hexdump -e '1/ "%d"' -s $(($LEN_BOOT-228)) -n 4 ${DEV_BOOT}`

# Load HDCP1x file
OFF_HDCP1X=$(($LEN_BOOT-236))
ADR_HDCP1X=$(($LEN_BOOT-7168))
LEN_HDCP1X=`hexdump -e '1/ "%d"' -s ${OFF_HDCP1X} -n 4 ${DEV_BOOT}`
dd if=${DEV_BOOT} of=${PATH_DRM}/hdcp1x.bin skip=${ADR_HDCP1X} bs=1 count=${LEN_HDCP1X} > /dev/null 2>&1

get_model model
if [ "$model" == "MAG351" ] || [ "$model" == "MAG352" ]; then
    # Load HDCP2x file
    OFF_HDCP2X=$(($LEN_BOOT-232))
    ADR_HDCP2X=$(($LEN_BOOT-5120))
    LEN_HDCP2X=`hexdump -e '1/ "%d"' -s ${OFF_HDCP2X} -n 4 ${DEV_BOOT}`
    dd if=${DEV_BOOT} of=${PATH_DRM}/hdcp2x.bin skip=${ADR_HDCP2X} bs=1 count=${LEN_HDCP2X} > /dev/null 2>&1
fi

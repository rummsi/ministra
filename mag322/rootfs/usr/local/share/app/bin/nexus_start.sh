#!/bin/sh

. /etc/init.d/shell-utils.sh

cd /usr/local/n/bin

export msg_modules=nexus_memory

get_model model

INITIAL_TV_MODE=""
get_fw_env "bootTVsystem" BOOT_TV_SYSTEM
if [ "${BOOT_TV_SYSTEM}" == "NTSC" ]; then
    INITIAL_TV_MODE="480p"
else
    INITIAL_TV_MODE="576p"
fi

if [ "$model" == "MAG322" ] || [ "$model" == "AuraHD4" ] || \
   [ "$model" == "IM2100V" ] || [ "$model" == "IM2100VI" ] || \
   [ "$model" == "IM2100" ]; then
    ./nexus ./nxserver -heap main,80M -transcode off -fbsize 1280,720 -evdev off -ir rc5 \
    -session0 sd,hd -hdcp1x_keys /ram/drm/hdcp1x.bin -memconfig display,capture=off \
    -memconfig display,5060=off -memconfig display,hddvi=off -memconfig display,mtg=off \
    -display_format $INITIAL_TV_MODE -videoDacDetection 2>&1 > /dev/null &
else
    ./nexus ./nxserver -transcode off -fbsize 1920,1080 -evdev off -ir rc5 -keypad on \
    -session0 sd,hd -hdcp1x_keys /ram/drm/hdcp1x.bin \
    -display_format $INITIAL_TV_MODE 2>&1 > /dev/null &
fi

export LD_LIBRARY_PATH=/usr/local/lib:/usr/local/share/app/lib:/usr/local/n/bin
i=0
while [ $i -lt 30000 ] ; do
    /usr/local/n/bin/wait_for_server -timeout 0 2>/dev/null
    if [ "$?" == "0" ] ; then
        break
    fi
    i=$((i+500))
    usleep 500000
done

#!/bin/sh

. /etc/utils/shell-utils.sh

cd /usr/local/n/bin

export msg_modules=nexus_memory

./nexus ./nxserver -transcode off -fbsize 1920,1080 -evdev off -ir rc5 -keypad on -session0 sd,hd 2>&1 \
-hdcp1x_keys /ram/drm/hdcp1x.bin -hdcp2x_keys /ram/drm/hdcp2x.bin > /dev/null &

#sleep 5
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

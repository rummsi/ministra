#!/bin/sh

source /etc/utils/shell-utils.sh

#export QT_QPA_EGLFS_DEPTH=32
#export QML2_IMPORT_PATH=/usr/local/bin/qt/qml
export FONTCONFIG_FILE=/usr/local/etc/fonts.conf
export LD_LIBRARY_PATH=/usr/local/lib:/usr/local/bin/qt/qml/QtMultimedia:/usr/local/share/app/lib:/usr/local/n/bin
export QT_QPA_PLATFORM_PLUGIN_PATH=/usr/local/bin/qt/plugins/
export QT_QPA_EGLFS_DISABLE_INPUT=1
export QT_NO_FT_CACHE=1

# check for certain variables
/usr/local/share/app/bin/STD_check_env.sh

# print start banner
dt=`date`
for i in {1..100};
do
    echo "[${dt}] StbApp is about to start..."
done

/usr/local/share/app/bin/info.sh > /ram/info.txt

# calculate graphic resolution
g_width=1280
g_heigth=720

gres=`fw_printenv graphicres`
gres=${gres#graphicres=}
gres=${gres## }
gres=${gres%% }

tvmode=`fw_printenv tvsystem`
tvmode=${tvmode#tvsystem=}

if [ -z ${gres} ]; then
    echo "Graphicres is undefined. defaulting to 1280..."
fi
echo "tvmode = ${tvmode}"
echo "graphicres = ${gres}"

case $gres in
    720)
        g_width=720
        g_heigth=576
    ;;
    1280)
        g_width=1280
        g_heigth=720
    ;;
    1920)
        g_width=1920
        g_heigth=1080
    ;;
    tvsystem_res)
        case $tvmode in
            pal|PAL)
                g_width=720
                g_heigth=576
            ;;
            ntsc|NTSC)
                g_width=720
                g_heigth=576
            ;;
            *)
                g_width=1280
                g_heigth=720
            ;;
        esac
    ;;
    *)
        g_width=720
        g_heigth=576
    ;;
esac
echo "Setting graphics resolution to ${g_width}:${g_heigth}"

# Setup LEDs
echo 5 > /sys/class/leds/mag-front-led/cnt

PORTAL_TO_LOAD="file:///usr/local/share/app/web/system/pages/loader/index.html"

for X in `cat /proc/cmdline`
do
    if [[ ${X:0:10} == "bootmedia=" ]]; then
        [ "${CMD_LINE}" ] && CMD_LINE="${CMD_LINE}&${X}" || CMD_LINE="${CMD_LINE}${X}"
    elif [[ ${X:0:14} == "fallbackstate=" ]]; then
        [ "${CMD_LINE}" ] && CMD_LINE="${CMD_LINE}&${X}" || CMD_LINE="${CMD_LINE}${X}"
    elif [[ ${X:0:9} == "btnstate=" ]]; then
        [ "${CMD_LINE}" ] && CMD_LINE="${CMD_LINE}&${X}" || CMD_LINE="${CMD_LINE}${X}"
    fi
done
[ "${CMD_LINE}" ] && PORTAL_TO_LOAD="${PORTAL_TO_LOAD}?${CMD_LINE}"

if [ ! "$@" == "" ]; then
    PORTAL_TO_LOAD=$@
fi
echo "Loading portal '$PORTAL_TO_LOAD'..."

./stbapp -platform "eglfs:width=${g_width}:height=${g_heigth}:zorder=5:xpos=0:ypos=0" \
         -plugin "EvdevKeyboard::repeat-delay=200:repeat-rate=30" -plugin "EvdevMouse:" $PORTAL_TO_LOAD

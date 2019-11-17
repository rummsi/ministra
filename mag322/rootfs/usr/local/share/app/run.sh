#!/bin/sh

selectGraphRes() {
    case $gres in
        720)
            g_width=720
            g_height=576
        ;;
        1280)
            g_width=1280
            g_height=720
        ;;
        1920)
            g_width=1920
            g_height=1080
        ;;
        tvsystem_res)
            echo "selected graphics as video"
            case $tvmode in
                pal|PAL|576p-50)
                    g_width=720
                    g_height=576
                ;;
                ntsc|NTSC)
                    g_width=720
                    g_height=480
                ;;
                720p-50|720p-60)
                    g_width=1280
                    g_height=720
                ;;
                1080i-50|1080p-50|1080p-60)
                    if [ "$limited_gres" == "true" ]; then
                        echo "DOWNGRADE GRES to 1280"
                        g_width=1280
                        g_height=720
                    else
                        g_width=1920
                        g_height=1080
                    fi
             ;;
             esac
        ;;
        *)
        ;;
    esac
}

source /etc/init.d/shell-utils.sh

#export QT_QPA_EGLFS_DEPTH=32
#export QML2_IMPORT_PATH=/usr/local/bin/qt/qml
export FONTCONFIG_FILE=/usr/local/etc/fonts.conf
export LD_LIBRARY_PATH=/usr/local/lib:/usr/local/bin/qt/qml/QtMultimedia:/usr/local/share/app/lib:/usr/local/n/bin
export QT_QPA_PLATFORM_PLUGIN_PATH=/usr/local/bin/qt/plugins/
export QT_QPA_EGLFS_DISABLE_INPUT=1
export QT_NO_FT_CACHE=1

get_model model

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
g_height=720

get_fw_env "graphicres" gres
get_fw_env "tvsystem" tvmode

if [ -z ${gres} ]; then
    echo "Graphicres is undefined. defaulting to 1280..."
fi

if [ "$model" == "MAG322" ] || [ "$model" == "AuraHD4" ] || \
   [ "$model" == "IM2100V" ] || [ "$model" == "IM2100VI" ] || \
   [ "$model" == "IM2100" ]; then
    limited_gres="true"
fi

if [ "$limited_gres" == "true" ]; then
    if [ "$gres" == "1920" ]; then
      echo "DOWNGRADE GRES to 1280"
      gres=1280
    fi
fi

echo "tvmode = ${tvmode}"
echo "graphicres = ${gres}"

/usr/local/n/bin/check-video-outputs
g_videoOutputsStatus=$?
echo "Video outputs status: $g_videoOutputsStatus"

case $tvmode in
    auto|Auto|AUTO)
    if [ "$g_videoOutputsStatus" = "1" ]; then
        echo "Only CVBS output active. Force GRES 720x576"
        g_width=720
        g_height=576
    else
        selectGraphRes
    fi
    ;;
    ntsc|NTSC)
        g_width=720
        g_height=480
    ;;
    *)
        selectGraphRes
    ;;
esac
echo "Setting graphics resolution to ${g_width}:${g_height}"

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

if [ ! -z "$@" ]; then
    PORTAL_TO_LOAD=$@
fi
echo "Loading portal '$PORTAL_TO_LOAD'..."

./stbapp -platform "eglfs:width=${g_width}:height=${g_height}:zorder=5:xpos=0:ypos=0" \
         -plugin "EvdevKeyboard::repeat-delay=200:repeat-rate=30" -plugin "EvdevMouse:" $PORTAL_TO_LOAD

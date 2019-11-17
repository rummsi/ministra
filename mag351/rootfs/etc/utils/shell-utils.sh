RESOLV_CONF="/ram/resolv.conf"
NTPCONFDEF="/etc/ntp/ntpd.conf.def"
NTPCONF="/ram/ntp/ntpd.conf"
TZCONF="/ram/timezone"
LTCONF="/ram/localtime"
RDIR_APP="/usr/local/share/app/bin/rdir.sh"
vend_dev="mmcblk0boot0" #MAG352/351/350/349

DEBUG_GLOBALS=""
if [ -f /ram/debug ]; then
    DEBUG="1"
    DEBUG_GLOBALS="G"
fi

exclude()
{
    if [ ! -f $1 ]; then
        return;
    fi
    A=`awk '{ if( index($0,"#'"$2"'") == 0 ) { print $0; }}' $1`
    echo "${A}" > $1
}

excludeRaw()
{
    if [ ! -f $1 ] || [ -z $2 ]; then
        return;
    fi
    A=`awk '{ if( index($0,"'"$2"'") == 0 ) { print $0; }}' $1`
    echo "${A}" > $1
}

selectByJsonPair()
{
    if [ ! -f $1 ] || [ -z $2 ]; then
        return;
    fi
    awk -vvar1=${2} '{ if( index($0,var1) != 0 ) { print $0; }}' $1
}

excludeByJsonPair()
{
    if [ ! -f $1 ] || [ -z $2 ]; then
        return;
    fi
    awk -vvar1=${2} '{ if( index($0,var1) == 0 ) { print $0; }}' $1
}

getMountPointByDevPath()
{
    A=$(selectByJsonPair ${MOUNTS_CACHE} "\"devPath\":\"${1}\"")
    cnt=`echo "${A}" | wc -l`
    if [ "$cnt" != "1" ]; then
        return
    fi
    echo "$A" | sed -n 's/^.*"mountPath":"\(.*\)",.*$/\1/pg'
}

excludeByDevPath()
{
    A=$(excludeByJsonPair ${1} "\"devPath\":\"${2}\"")
    echo "${A}" > ${1}
}

isNfsMount()
{
    if [ `cat /proc/cmdline | grep nfsroot | wc -l` == "1" ]; then
        export $1="y"
    else
        export $1="n"
    fi

    return
}

log()
{
    if [ "$DEBUG" != "1" ] && [ -z "$DEBUG_GLOBALS" ]; then
        return
    fi

    if [ ! -d /var/logs/ ]; then
        return
    fi

    if [ -z "$LOG_FILE" ]; then
        LOG_FILE="/var/logs/$(basename "$0").log"
    fi

    if [ -n "$DEBUG_GLOBALS" ]; then
        echo `date +%H:%M.%S`" [$DEBUG_GLOBALS][$(basename "$0")] "$1 >> $LOG_FILE
    else
        echo `date +%H:%M.%S`" [$(basename "$0")] "$1 >> $LOG_FILE
    fi
}

get_nvram_env()
{
    tmpVal=`fw_printenv $1 2>/dev/null`
    FW_ENV_RESULT=${tmpVal#*=}
}

read_nvram_var()
{
    get_nvram_env $1
    echo "$FW_ENV_RESULT"
}

get_fw_env()
{
    val=`fw_printenv $1 2>/dev/null`
    export $2="${val#$1=}"
}

# appending config line to current NTP config. 
# create default config if file not found
append_ntp_config()
{
    if [ ! -f $NTPCONF ]; then
        cat $NTPCONFDEF > $NTPCONF
        NTP_IP=$(read_nvram_var "ntpurl")
        if [ "$NTP_IP" != "" ]; then
            echo "server $NTP_IP #manual" >> $NTPCONF
        fi
    fi

    echo $1 >> $NTPCONF
}

# update default gateways for interface "$1" with new default gw list "$2" using metric "$3"
update_default_gw_for_interface()
{
    if [ -n "$2" ]; then

        # get currently active routers
        WORK_ROUTERS=`ip route | sed -n "s/^default via \([^ ]*\) dev ${1}.*$/\1/p"`
        #log "WORK_ROUTERS = ${WORK_ROUTERS}"

        # list of routers to delete
        ROUTERS_TO_DEL="";
        for i in $WORK_ROUTERS
        do
            isFound="0"
            #checking work router
            for j in $2
            do
                #compape with new router
                if [ "${i}" = "${j}" ]; then
                    isFound="1"
                    break
                fi
            done

            if [ "$isFound" = "0" ]; then
                #store router $i for deletion
                ROUTERS_TO_DEL="${ROUTERS_TO_DEL}${i} "
            fi
        done

        # add new routers
        for i in ${2}
        do
            #add router $i
            #log "add default gw ${i}, ${1}, ${3}"
            /sbin/route add default gw ${i} dev ${1} metric ${3}
        done

        # delete unneeded routers
        for i in $ROUTERS_TO_DEL
        do
            #DEL router ${i}
            #log "del default gw ${i}"
            /sbin/route del default gw ${i}
        done
    fi
}

get_offset()
{
    len=`cat /sys/class/block/${vend_dev}/size 2>/dev/null`
    export $1=$((len*512-224))
}

get_bt_mac()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_wifi_mac()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+32)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_serial()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+64)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_eth_mac()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+96)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_vendor()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+128)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_model()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+160)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_hwver()
{
    get_offset offs
    tmp=`dd if=/dev/$vend_dev bs=1 count=32 skip=$(($offs+192)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

read_opkey_from_vs()
{
    get_offset offs
    offs=$(($offs-11040))
    flag=`hexdump -e '1/ "%d"' -s $offs -n 4 /dev/$vend_dev`
    if [ "$flag" -ne "0" ]; then
        echo -n `dd if=/dev/$vend_dev bs=1 count=4096 skip=$offs 2>/dev/null` > $1
    else
        echo -n "" > $1
    fi
}

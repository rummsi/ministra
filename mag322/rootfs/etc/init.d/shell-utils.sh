RESOLV_CONF="/ram/resolv.conf"
NTPCONFDEF="/etc/ntp/ntpd.conf.def"
NTPCONF="/ram/ntp/ntpd.conf"
TZCONF="/ram/timezone"
LTCONF="/ram/localtime"

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

TIME_STAMP=`cat /proc/uptime | cut -d" " -f 1`
    if [ -n "$DEBUG_GLOBALS" ]; then
        echo "$TIME_STAMP [$DEBUG_GLOBALS][$(basename "$0"):$$] "$1 >> $LOG_FILE
#        echo `date +%H:%M.%S%N`" [$DEBUG_GLOBALS][$(basename "$0")] "$1 >> $LOG_FILE
    else
        echo "$TIME_STAMP [$(basename "$0"):$$] "$1 >> $LOG_FILE
#        echo `date +%H:%M.%S%N`" [$(basename "$0")] "$1 >> $LOG_FILE
    fi
}

get_fw_env()
{
    val=`fw_printenv $1 2>/dev/null`
    export $2="${val#$1=}"
}

set_fw_env()
{
    fw_setenv $1 $2 2>&1 >/dev/null
}

# appending config line to current NTP config. 
# create default config if file not found
append_ntp_config()
{
    if [ ! -f $NTPCONF ]; then
        cat $NTPCONFDEF > $NTPCONF
        get_fw_env "ntpurl" NTP_IP
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

get_name_and_len_part() {
    str=`grep \"$1\" /proc/mtd`
    out2=`echo "${str}" | grep -oE 'mtd[0-9]{1,}'`
    out1=`echo "${str}" | awk '{print $2}'`
    if [ "$out1" != "" ]; then out1=$((0x${out1})); fi
    export $2=$out1
    export $3=$out2
}

get_hwver() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-32)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_model() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-64)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    if [ "$tmp" == "IM2100" ]; then
        tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-32)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
        if [ "${tmp:4:2}" == "VA" ]; then
            tmp="IM2100V"
        elif [ "${tmp:4:2}" == "VI" ]; then
            tmp="IM2100VI"
        else
            tmp="IM2100"
        fi
    fi
    export $1=$tmp
}

get_vendor() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-96)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_eth_mac() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-128)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_serial() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-160)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_wifi_mac() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-192)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_bt_mac() {
    get_name_and_len_part "bootloader" offs name
    tmp=`dd if=/dev/$name bs=1 count=32 skip=$(($offs-224)) 2>/dev/null | strings -n1 | awk '{printf "%s" $0; exit;}'`
    export $1=$tmp
}

get_otp_id() {
    get_name_and_len_part "bootloader" offs name
    tmp=`hexdump -e '8/1 "%02X"' -s $(($offs-244)) -n 8 /dev/$name`
    export $1=$tmp
}

read_opkey_from_vs() {
    get_name_and_len_part "bootloader" offs name
    offs=$(($offs-11264))
    flag=`hexdump -e '1/ "%d"' -s $offs -n 4 /dev/$name`
    if [ "$flag" -ne "0" ]; then
        echo -n `dd if=/dev/$name bs=1 count=4096 skip=$offs 2>/dev/null` > $1
    else
        echo -n "" > $1
    fi
}

load_hdcp1x_from_vs() {
    get_name_and_len_part "bootloader" offs name
    addr=$(($offs-7168))
    offs=$(($offs-236))
    len=`hexdump -e '1/ "%d"' -s $offs -n 4 /dev/$name`
    dd if=/dev/$name of=$1 skip=$addr bs=1 count=$len > /dev/null 2>&1
}

load_hdcp2x_from_vs() {
    get_name_and_len_part "bootloader" offs name
    addr=$(($offs-5120))
    offs=$(($offs-232))
    len=`hexdump -e '1/ "%d"' -s $offs -n 4 /dev/$name`
    dd if=/dev/$name of=$1 skip=$addr bs=1 count=$len > /dev/null 2>&1
}

get_dev_for_name() {
    if [ "$1" == "Rootfs" ] ; then
        tmp="ubi0_0"
    elif [ "$1" == "Rootfs2" ] ; then
        tmp="ubi0_1"
    elif [ "$1" == "Userfs" ] ; then
        tmp="ubi0_2"
    else
        tmp=`grep \"$1\" /proc/mtd | grep -oE 'mtd[0-9]{1,}'`
    fi
    export $2=$tmp
}

get_file_size() {
    tmp=`stat -c %s $1 2>/dev/null`
    tmp=$((tmp))
    export $2=$tmp
}

get_flash_type() {
  tmp=`mtdinfo $1 | grep "Type:"`
  export $2=${tmp:32}
}

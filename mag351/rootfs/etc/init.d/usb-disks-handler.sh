#!/bin/sh

. /etc/utils/shell-utils.sh
DEBUG=0

retVal=0
MOUNTS_CACHE="/ram/mounts.cache"
QT_SEND_EVENT=/usr/local/share/app/bin/sendqtevent
SATA_DEV="null"
MEDIA_TYPE="0"
DEV_PATH_ATTR_NAME="devPath"

MEDIA_TYPE_SD="3"
MEDIA_TYPE_MMC="4"

NTFSLABEL_PROG="/root/bin/ntfslabel"
VAR_FORCE_RO="mount_media_ro"

UDEVADM_BIN=/sbin/udevadm

filterSnString()
{
    SERIAL=`echo -n \"$1\" | strings | awk '{ s=$0; gsub(/[^a-zA-Z0-9_]/, "", s); printf s }'`
    if [ "$SERIAL" == "" ]; then
        SERIAL="UNASSIGNED"
    fi
}

getCodePageCurrent(){
    currLang=$(read_nvram_var "language")
    case ${currLang} in
        "de")
            echo -n "850"
        ;;
        "ru")
            log "using cp 866..."
            echo -n "866"
        ;;
        *)
            log "using default cp 866..."
            echo -n "866"
        ;;
    esac
}

getDiskLabel() {
    diskOfInterest="/dev/${1}"
    #echo "diskOfInterest = '${diskOfInterest}'"
    case ${2} in
        "ntfs")
            ${NTFSLABEL_PROG} -nf ${diskOfInterest} 2>/dev/null
        ;;
        "ext2")
        ;;
        "ext3")
        ;;
        "vfat")
            blkid -c /ram/blkid.tab ${diskOfInterest} | /root/bin/iconv -f $(getCodePageCurrent) -t utf8 | sed -n 's/^.*LABEL="\([^"]*\).*/\1/gp'
        ;;
        *)
            echo -n "UNKNOWN"
        ;;
    esac
}

getFsTypeIndex() {
    case ${1} in
        "ntfs")
            echo -n "5"
        ;;
        "ext2")
            echo -n "3"
        ;;
        "ext3")
            echo -n "4"
        ;;
        "vfat")
            echo -n "2"
        ;;
        *)
            echo -n "0"
        ;;
    esac
}


# EXECUTION POINT

log "devName='${DEV_NAME}', sn='${SERIAL}', pNum='${PNUM}', vendor='${uVendor}', model='${uModel}', DevPath='${DEVPATH}'"

#ignore system mmc partitions
mmc_dev=`echo ${DEV_NAME} | grep -o -m1 mmcblk0`
if [ "$mmc_dev" == "mmcblk0" ] ; then
    exit 0
fi

# get SERIAL
#SERIAL=`${UDEVADM_BIN} info -q property -x -a -p "${DEVPATH}" 2>&1`
SERIAL=`${UDEVADM_BIN} info -q property -x -a -p "${DEVPATH}" | grep "ATTRS{serial}==" | awk 'NR==1{print $0}' | sed -n 's/^.*=="\(.*\)"$/\1/pg'`
#SERIAL=`udevadm info --query=property --root --name=${DEV_NAME} | grep "^ID_SERIAL_SHORT="`
#SERIAL=${SERIAL#ID_SERIAL_SHORT=}
log "Got SERIAL=${SERIAL}"

# get VENDOR
uVendor=`${UDEVADM_BIN} info -q property -x -a -p "${DEVPATH}" | grep "ATTRS{vendor}==" | awk 'NR==1{print $0}' | sed -n 's/^.*=="\(.*\)"$/\1/pg'`
log "Got uVendor=${uVendor}"

# get MODEL
uModel=`${UDEVADM_BIN} info -q property -x -a -p "${DEVPATH}" | grep "ATTRS{model}==" | awk 'NR==1{print $0}' | sed -n 's/^.*=="\(.*\)"$/\1/pg'`
uModel=${uModel#ID_MODEL=}
log "Got uModel=${uModel}"

case $1 in
    add)
        log "add command"

        SATA_DEV=`echo "'${DEVPATH}'" | awk '{ i1 = index($0, "/sata-stm/"); if(i1 > 0){print "sata"  }}'`;
        if [ "$SATA_DEV" == "sata" ]; then
            if [ ! -f /ram/satadev ]; then
                SATA_DEVICE_OF_INTEREST=`echo -n "/dev/${DEV_NAME}" | sed -n 's/^\(\/dev\/sd[a-z]\).*$/\1/pg'`
            if [ ! -z "${SATA_DEVICE_OF_INTEREST}" ]; then
                    echo -n "${SATA_DEVICE_OF_INTEREST}" > /ram/satadev
                fi
            fi
        fi

        size=`cat /sys${DEVPATH}/size 2> /dev/null`;
        if [ -z "$size" ]; then
            log "Size read error!"
            return
        fi
        size=$(( size * 512 ));

        #label=`blkid -c /ram/blkid.tab /dev/${DEV_NAME} | awk '{ i1 = index($0, " LABEL=\""); if(i1 > 0){ s1 = substr($0, i1 + 8); i2 = index(s1, "\""); if (i2 > 0){print substr(s1, 0, i2 - 1);} } }'`;
        fstype=`blkid -c /ram/blkid.tab /dev/${DEV_NAME} | awk '{ i1 = index($0, " TYPE=\""); if(i1 > 0){ s1 = substr($0, i1 + 7); i2 = index(s1, "\""); if (i2 > 0){print substr(s1, 0, i2 - 1);} } }'`;
        fsTypeIndex=$(getFsTypeIndex ${fstype})

        label=$(getDiskLabel ${DEV_NAME} ${fstype})
        log "fstype=${fstype}, fsTypeIndex=${fsTypeIndex}, label=${label}"

        if [ "$fstype" == "" ]; then
            log "fstype is empty!"
            exit -1
        fi

        # count "entire disk" as single partition number 0
        if [ "$PNUM" == "" ]; then
            PNUM="0"
        fi

        # serial number conditioning
        filterSnString $SERIAL

        FULL_NAME="USB-${SERIAL}-${PNUM}"

        # handle SD cards
        if [ -n "`echo $DEV_NAME | sed -n 's/^\(mmc\).*$/\1/pg'`" ];
        then
            MEDIA_TYPE=${MEDIA_TYPE_SD}
            FULL_NAME="SD-${SERIAL}-${PNUM}"
            if [ "$uModel" == "" ] && [ "$uVendor" == "" ] ; then
                uModel="SD Card"
            fi
        else
            if [ "$SERIAL" == "UNASSIGNED" ]; then
                FULL_NAME="HDD-SATA-${PNUM}"
                SERIAL="HDD-SATA-${PNUM}"
                MEDIA_TYPE="1"
            else
                MEDIA_TYPE="2"
            fi
        fi


        FOLDER_TO_CREATE="/media/${FULL_NAME}"
        log "mount point '$FOLDER_TO_CREATE'"

        # clean old mounts
        excludeByDevPath ${MOUNTS_CACHE} ${DEVPATH}
        while [ 1 ];
        do
            umount -l $FOLDER_TO_CREATE 2>/dev/null
            if [ $? != 0 ];
                then break;
            fi
        done

        if [ ! -f "$FOLDER_TO_CREATE" ]; then
            log "Creating mount point..."
            mkdir "$FOLDER_TO_CREATE"
        fi

        log "Create OK"

        IS_READ_ONLY="0"
        EXTRA_OPT="noatime,nodiratime"

        # check for force RO mode
        isRO=$(read_nvram_var $VAR_FORCE_RO)
        if [ "$isRO" == "true" ]; then
            MOUNT_OPT="ro";
        else
            MOUNT_OPT="rw";
        fi

        case $fstype in
            "ntfs")
                DEV_MODEL=`$RDIR_APP Model`
                log "/bin/ntfs-3g -o ro,iocharset=utf8,$EXTRA_OPT $DEVNAME \"$FOLDER_TO_CREATE\""
                /bin/ntfs-3g -o iocharset=utf8,$EXTRA_OPT $DEVNAME "$FOLDER_TO_CREATE"
                #IS_READ_ONLY="1"
            ;;
            "ext2")
                mount -o $MOUNT_OPT,$EXTRA_OPT $DEVNAME "$FOLDER_TO_CREATE"
            ;;
            "ext3")
                mount -o $MOUNT_OPT,$EXTRA_OPT $DEVNAME "$FOLDER_TO_CREATE"
            ;;
            "vfat")
                mount -o $MOUNT_OPT,iocharset=utf8,$EXTRA_OPT $DEVNAME "$FOLDER_TO_CREATE"
            ;;
            *)
                mount -o $MOUNT_OPT,$EXTRA_OPT $DEVNAME "$FOLDER_TO_CREATE"
            ;;
        esac

        RET=$?
        if [ "$RET" != "0" ]; then
            log "mount error! removing mount point..."
            rmdir "$FOLDER_TO_CREATE"
            exit -1
        fi

        log "mount OK"
        freeSize=`df -k | grep /ram/media/$FULL_NAME | awk '{print $4}'`
        if [ -z "${freeSize}" ]; then
            freeSize=0
        else
            freeSize=$((${freeSize}*1024))
        fi

        READ_ONLY_STATUS=`mount | grep /ram/media/$FULL_NAME | sed -n "s/^.*(\(rw\).*$/\1/p"`
        log "READ_ONLY_STATUS=${READ_ONLY_STATUS}"
        if [ "${READ_ONLY_STATUS}" = "rw" ]; then
            IS_READ_ONLY="0"
        else
            IS_READ_ONLY="1"
        fi

        log "creating cache record"
        echo "key:${FULL_NAME}:{\"sn\":\"${SERIAL}\",\"vendor\":\"${uVendor}\",\"model\":\"${uModel}\",\"size\":${size},\"freeSize\":${freeSize},\"label\":\"${label}\",\"partitionNum\":${PNUM},\"isReadOnly\":${IS_READ_ONLY},\"mountPath\":\"${FOLDER_TO_CREATE}\",\"mediaType\":${MEDIA_TYPE},\"fsType\":${fsTypeIndex},\"${DEV_PATH_ATTR_NAME}\":\"${DEVPATH}\"}" >> $MOUNTS_CACHE
        ${QT_SEND_EVENT} -a -ks 0x70 -kqt 0x50
    ;;
    remove)
        log "remove command"

        # count "entire disk" as single partition number 0
        if [ -z "$PNUM" ]; then
            PNUM="0"
        fi

        # serial number conditioning
        filterSnString $SERIAL

        MOUNT_POINT=$(getMountPointByDevPath ${DEVPATH})
        log "MOUNT_POINT=$MOUNT_POINT"

        if [ -d ${MOUNT_POINT} ] && [ -n "${MOUNT_POINT}" ]; then
            log "Unmounting '${MOUNT_POINT}'..."
            umount -l "${MOUNT_POINT}"
            log "Removing '${MOUNT_POINT}'..."
            rmdir "${MOUNT_POINT}"
            excludeByDevPath ${MOUNTS_CACHE} ${DEVPATH}
            ${QT_SEND_EVENT} -a -ks 0x71 -kqt 0x51
        fi
        if [ "$SERIAL" == "UNASSIGNED" ]; then
           if [ -d /ram/media/HDD-SATA-${PNUM} ]; then
             FULL_NAME="HDD-SATA-${PNUM}"
             FOLDER_TO_DELETE="/media/${FULL_NAME}"
             log "Unmounting '$FOLDER_TO_DELETE'..."
             umount -l "$FOLDER_TO_DELETE"
             log "Removing '$FOLDER_TO_DELETE'..."
             rmdir "$FOLDER_TO_DELETE"
             excludeRaw $MOUNTS_CACHE $FULL_NAME
             ${QT_SEND_EVENT} -a -ks 0x71 -kqt 0x51
            fi
        fi
    ;;
    change)
        log "change command"

        label=`blkid -c /ram/blkid.tab /dev/${DEV_NAME}`;

        log "done"
    ;;
esac

exit 0

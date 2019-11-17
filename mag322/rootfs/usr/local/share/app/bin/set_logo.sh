#!/bin/sh

source /etc/init.d/shell-utils.sh

upload_logo=/ram/tmp_logo.img
cmp_logo=/ram/cmp_logo.img

if [ "$1" == "" ]; then
    echo "ERROR: PATH is null"
    exit 1;
fi

get_name_and_len_part "logo" max name

up_url=$1
rm -f $upload_logo 2>/dev/null 1>/dev/null


URL_HTTP=`echo $up_url |awk '{ if($1~/^http:\/\//) {print $1; exit; } }'`
TFTP_IP=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,1,i-1); exit; } }'`
TFTP_ROOTPATH=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,i+1); exit; } }'`

if [ "$URL_HTTP" == "" ]; then
    if [ "$TFTP_IP" == "" ] || [ "$TFTP_ROOTPATH" == "" ]; then
        cp $1 $upload_logo
    else
        tftp -l /proc/self/fd/1 -g -r $TFTP_ROOTPATH $TFTP_IP 2>/dev/null | dd of=$upload_logo bs=1 count=$max 2>/dev/null 1>/dev/null
    fi
else
    wget $URL_HTTP  -O /dev/stdout 2>/dev/null | dd of=$upload_logo bs=1 count=$max 2>/dev/null 1>/dev/null
fi

get_file_size "$upload_logo" size

if [ $size -eq 0 ]; then
    echo "ERROR: Uploading is failed."
    exit 1;
fi

if [ $max -lt $size ]; then
    echo "ERROR: File size more then $max byte."
    exit 1;
fi

get_flash_type /dev/$name type
if [ $type == "nand" ]; then
    nanddump /dev/$name -l $size -f $cmp_logo 2>/dev/null
    dd if=/dev/null of=$cmp_logo bs=1 count=0 seek=$size 2>/dev/null
    cmp -s $cmp_logo $upload_logo
    if [ ! "$?" -eq "0" ]; then
        flash_erase /dev/$name 0 0 >/dev/null
        nandwrite -pam /dev/$name $upload_logo >/dev/null
        if [ "$?" -ne "0" ]; then
            echo "REPLACE LOGO ERROR: write to NAND flash failed!!!"
            exit 1
        fi
    fi
else
    dd if=/dev/$name ibs=1 of=$cmp_logo obs=1 count=$size 2>/dev/null
    cmp -s $cmp_logo $upload_logo
    if [ ! "$?" -eq "0" ]; then
        flash_erase /dev/$name 0 0 >/dev/null
        cat $upload_logo > /dev/$name
        if [ "$?" -ne "0" ]; then
            echo "REPLACE LOGO ERROR: write to NOR flash failed!!!"
            exit 1
        fi
    fi
fi

rm -f $upload_logo $cmp_logo 1>/dev/null 2>/dev/null
get_fw_env "showlogo" tmp
if [ "$tmp" != "yes" ]; then
    set_fw_env showlogo yes
fi
echo "OK"
exit 0

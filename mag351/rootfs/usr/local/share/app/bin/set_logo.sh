#!/bin/sh
get_file_size() {
    size=`stat -c %s $1 2>/dev/null`
    size=$((size))
    export $2=$size
}

upload_logo=/ram/tmp_logo.img
cmp_logo=/ram/cmp_logo.img
oldlogo=/ram/oldlogo.img
newlogo=/ram/newlogo.img
MTD_NAME="mmcblk0p2"


len=`cat /sys/class/block/${MTD_NAME}/size 2</dev/null`
max=$((len*512))

if [ "$1" == "" ]; then 
    echo "ERROR: PATH is null"
    exit 1;
fi

up_url=$1
rm -f $upload_logo 2>/dev/null 1>/dev/null


URL_HTTP=`echo $up_url |awk '{ if($1~/^http:\/\//) {print $1; exit; } }'`
TFTP_IP=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,1,i-1); exit; } }'`
TFTP_ROOTPATH=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,i+1); exit; } }'`

if [ "$URL_HTTP" == "" ]; then
    if [ "$TFTP_IP" == "" ] || [ "$TFTP_ROOTPATH" == "" ]; then
#        echo "ERROR: Wrong path."
#        exit 1
        cp $1 $upload_logo
    else
#        echo "Upload file from IP "$TFTP_IP" path "$TFTP_ROOTPATH" by tftp"
        tftp -l /proc/self/fd/1 -g -r $TFTP_ROOTPATH $TFTP_IP 2>/dev/null | dd of=$upload_logo bs=1 count=$max 2>/dev/null 1>/dev/null
    fi
else
#    echo "Upload file from url "$URL_HTTP
    wget $URL_HTTP  -O /dev/stdout 2>/dev/null | dd of=$upload_logo bs=1 count=$max 2>/dev/null 1>/dev/null
fi

get_file_size "$upload_logo" size

if [ $size -eq 0 ]; then
    echo "ERROR: Uploading is failed."
    exit 1;
fi

if [ $max -lt $size ]; then
    echo "ERROR: File size more then 49152."
    exit 1;
fi

#cat /dev/$MTD_NAME > $oldlogo
#rm -f $cmp_logo 2>/dev/null 1>/dev/null
#dd if=$oldlogo ibs=1 of=$cmp_logo obs=1 count=$size 2>/dev/null 1>/dev/null
#cmp -s $cmp_logo $upload_logo
#if [ ! "$?" -eq "0" ]; then
#    cat $upload_logo > $newlogo
#    flash_eraseall /dev/$MTD_NAME 2>/dev/null2>/dev/null
#    cat $newlogo > /dev/$MTD_NAME
#    rm -f $newlogo 2>/dev/null 1>/dev/null
#fi
cat $upload_logo > /dev/$MTD_NAME

if [ "$?" -ne "0" ]; then
    echo "REPLACE LOGO ERROR: write to emmc failed!!!"
    exit 1
fi

rm -f $oldlogo $upload_logo $cmp_logo 1>/dev/null 2>/dev/null
tmp=`fw_printenv showlogo 2>/dev/null`
tmp=${tmp#showlogo=}
if [ "$tmp" != "yes" ]; then
    fw_setenv showlogo yes 2>/dev/null 1>/dev/null
fi
echo "OK"
exit 0 

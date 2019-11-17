#!/bin/sh

source /etc/init.d/shell-utils.sh

upload_sboot=/ram/SbootIm
head_sboot=/ram/SbootHead
one_sboot=/ram/one_sboot.img
cmp_sboot=/ram/cmp_sboot.img

get_name_and_len_part "ssbl2" max name_part

if [ "$1" != "" ]; then
    upload_sboot=$1
fi

if [ ! -e $upload_sboot ]; then
    echo "ERROR: File $upload_sboot not present!!!"
    exit 1
fi

get_file_size "$upload_sboot" size

if [ $size -eq 0 ]; then
    echo "ERROR: Uploading bootloader is failed."
    exit 1;
fi

get_flash_type /dev/$name_part type
if [ $type == "nand" ]; then

    rm -f $head_sboot
    dd if=$upload_sboot of=$head_sboot bs=1K count=1 2>/dev/null
    offset=`awk '{if($0 == "Start data..."){s+=length($0)+1;print s;exit;}else{s+=length($0)+1;}}' $head_sboot` 2>/dev/null
    dd if=/dev/null of=$head_sboot bs=1 count=0 seek=$offset 2>/dev/null

    get_model model
    get_hwver hwver
    if [ "$model" == "IM2100V" ]; then
        type="IM2100V"
    if [ "$model" == "IM2100VI" ]; then
        type="IM2100VI"
    elif [ "$model" == "MAG325" ]; then
        type="MAG325"
    elif [ "$model" == "MAG325C" ]; then
        type="MAG325"
    elif [ "$model" == "MAG322" ]; then
        if  echo $hwver | grep -q "UP-"; then
            type="MAG322_UP"
        else
            type="MAG322"
        fi
    elif [ "$model" == "AuraHD4" ]; then
        if echo $hwver | grep -q "UP-"; then
            type="AuraHD4_UP"
        else
            type="AuraHD4"
        fi
    elif [ "$model" == "MAG324" ]; then
        if echo $hwver | grep -q "UP-"; then
            type="MAG324_UP"
        else
            type="MAG324"
        fi
    elif [ "$model" == "MAG324C" ]; then
        if echo $hwver | grep -q "UP-"; then
            type="MAG324C_UP"
        else
            type="MAG324C"
        fi
    elif [ "$model" == "IM2100" ]; then
        if [ "${hwver:2:1}" == "U" ]; then
            type="IM2100_UP"
        else
            type="IM2100"
        fi
    elif [ "$model" == "IM2101" ]; then
        if [ "${hwver:2:1}" == "U" ]; then
            type="IM2101_UP"
        else
            type="IM2101"
        fi
    elif [ "$model" == "IM2102" ]; then
        if [ "${hwver:2:1}" == "U" ]; then
            type="IM2102_UP"
        else
            type="IM2102"
        fi
    fi

    tmp=`grep -w "$type" $head_sboot` 2>/dev/null
    len=`echo "$tmp" | awk '{print $2}'`
    pos=`echo "$tmp" | awk '{print $3}'`
    echo "======[$type] [$len] [$pos] [$offset]======"
    if [ "$pos" != "" ] && [ "$len" != "" ]; then

        dd if=$upload_sboot of=$one_sboot skip=$(($pos+$offset)) bs=1 count=$len

        nanddump /dev/$name_part -l $len -f $cmp_sboot 2>/dev/null
        dd if=/dev/null of=$cmp_sboot bs=1 count=0 seek=$len 2>/dev/null

        cmp -s $cmp_sboot $one_sboot
        if [ ! "$?" -eq "0" ]; then
            flash_erase /dev/$name_part 0 0 >/dev/null
            nandwrite -pam /dev/$name_part $one_sboot >/dev/null
            if [ "$?" -ne "0" ]; then
                echo "ERROR: Write to NAND flash failed!!!"
                exit 1
            fi
        fi
    fi
fi

rm -f $upload_sboot $head_sboot $one_sboot $cmp_sboot
echo "OK"
exit 0

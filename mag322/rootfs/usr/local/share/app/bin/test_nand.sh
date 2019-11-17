#!/bin/sh

total=0
cnt=`mtdinfo -B /dev/block/by-name/bootloader | awk '{print $3}'`
if [ ${cnt} -gt "0" ]; then
    echo "NANAD TEST ERROR: In \"bootloader\" found $cnt bad block"
    exit 1
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/ssbl | awk '{print $3}'`
if [ ${cnt} -gt "0" ]; then
    echo "NANAD TEST ERROR: In \"ssbl\" found $cnt bad blocks"
    exit 2
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/ssbl2 | awk '{print $3}'`
if [ ${cnt} -gt "0" ]; then
    echo "NANAD TEST ERROR: In \"ssbl2\" found $cnt bad blocks"
    exit 3
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B -o 00000000 -s 000c0000 /dev/block/by-name/env | awk '{print $3}'`
if [ ${cnt} -gt "1" ]; then
    echo "NANAD TEST ERROR: In \"env1\" found $cnt bad blocks"
    exit 4
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B -o 000c0000 -s 000c0000 /dev/block/by-name/env | awk '{print $3}'`
if [ ${cnt} -gt "1" ]; then
    echo "NANAD TEST ERROR: In \"env2\" found $cnt bad blocks"
    exit 5
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/logo | awk '{print $3}'`
if [ ${cnt} -gt "1" ]; then
    echo "NANAD TEST ERROR: In \"logo\" found $cnt bad blocks"
    exit 6
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/Kernel | awk '{print $3}'`
if [ ${cnt} -gt "5" ]; then
    echo "NANAD TEST ERROR: In \"Kernel\" found $cnt bad blocks"
    exit 7
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/Kernel2 | awk '{print $3}'`
if [ ${cnt} -gt "5" ]; then
    echo "NANAD TEST ERROR: In \"Kernel2\" found $cnt bad blocks"
    exit 8
fi

total=$(($total+$cnt))
cnt=`mtdinfo -B /dev/block/by-name/raw | awk '{print $3}'`
if [ ${cnt} -gt "100" ]; then
    echo "NANAD TEST ERROR: In \"raw\" found $cnt bad blocks"
    exit 9
fi

total=$(($total+$cnt))
if [ ${total} -gt "110" ]; then
    echo "NANAD TEST ERROR: In NAND found $total bad blocks"
    exit 10
fi

echo "NANAD TEST OK"
exit 0

#!/bin/sh

source /etc/utils/shell-utils.sh
export DISGN_SHA_OPT="-h"
p3="/ram/env_format.KEY"
p4="/ram/radix64.KEY"
p5="/ram/oppubbin.KEY"

# Read Operator's key from vendor section
read_opkey_from_vs $p3

fsize=`wc -c $p3`
size2=${fsize%%$p3}
size2=$((size2))
if [ "$?" -ne "0" ] || [ "$size2" == "0" ]; then

    # Read Operator's key from environment
    fw_printenv oppubKEY > $p3
    if [ "$?" -ne "0" ]; then
        rm -f $p3 $p4 2>/dev/null 1>/dev/null
        echo "Error: Read environment"
        exit 1
    fi

    fsize=`wc -c $p3`
    size2=${fsize%%$p3}
    size2=$((size2))
    if [ "$?" -ne "0" ] || [ "$size2" == "0" ]; then
        rm -f $p3 $p4 2>/dev/null 1>/dev/null
        echo "Error: Not found operator's key in environment"
        exit 1
    fi
    tail -c $(($size2-9)) $p3 > $p4
else
    cp -f $p3 $p4
fi

cd /ram
dsign -n $p4
if [ "$?" -ne "0" ]; then
    rm -f $p3 $p4 2>/dev/null 1>/dev/null
    echo "Error: Not correct operator's key"
    exit 1
fi

fsize=`wc -c $p4`
size=${fsize%%$p4}
dsign -c $DISGN_SHA_OPT --len=$(($size-263)) --signoffset=$(($size-263)) -p /usr/bin/pubbin.KEY $p4
if [ "$?" -ne "0" ]; then
    rm -f $p3 $p4 2>/dev/null 1>/dev/null
    echo "Error: Not correct digital signature operator's key"
    exit 1
fi

head -c $(($size-263)) $p4 > $p5
rm -f $p3 $p4 2>/dev/null 1>/dev/null
echo "Correct Digital signature"
exit 0
# Make bootimage: End

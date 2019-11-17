#!/bin/sh
#

inputSN='';
inputPNum='';

log()
{
  if [ $DEBUG -eq 1 ]; then
    echo $1
    echo `date`   "[] "$1 >> $LOG_FILE
  fi
}

trim()
{
    trimmed=$1
    trimmed=${trimmed%% }
    trimmed=${trimmed## }

    echo $trimmed
}

parseHddName()
{
    inputSN=`echo $1 | awk '{s1 = substr($0, 12); i1 = index(s1, "-"); if (i1 < 2){print "undefined"} else { s2 = substr(s1, 0, i1-1); print s2 } }'`;
    inputPNum=`echo $1 | awk '{i1 = index($0, "-"); if (i1 > 0){ s1 = substr($0, i1+1); i2 = index(s1, "-"); if(i2>0){ print substr(s1, i2 + 1) } } }'`;
}

# Input $1 - HDD id as it exist in /media/USB*
# output - description of disk for user

DEBUG=0
LOG_FILE=/ram/hdd-get-info.txt
MOUNTS_CACHE="/ram/mounts.cache"

parseHddName "/media/$1";
#echo ${inputSN}
#echo ${inputPNum}

if [ ! -f ${MOUNTS_CACHE} ]; then
    echo "[]"
    exit 0
fi
if [ "$1" == "" ]; then
    flock $MOUNTS_CACHE echo "`awk 'BEGIN {f = 0; printf("[") } { i1 = index($0, "{"); if(i1>0) { if(f > 0){printf(",");}; printf("%s", substr($0, i1)); f++;} }; END{ printf("]") }' ${MOUNTS_CACHE}`"
else
    freeSize=`df -k | awk '{if($6 == "/ram/media/'$1'") {print $4}}'`;
    freeSize=$((${freeSize}*1024))
    TEXT="s/\([0-9]*,\)\"freeSize\":[0-9]*/\1\"freeSize\":$freeSize/g";
    flock $MOUNTS_CACHE echo "`awk -vvar1="$1" 'BEGIN{FS = ":"}; { if( $2 == var1) { i1 = index($0, "key:"var1); if(i1>0) {i2 = 2+length("key:"var1); print substr($0, i2)} } }' $MOUNTS_CACHE`" | sed -e $TEXT
fi

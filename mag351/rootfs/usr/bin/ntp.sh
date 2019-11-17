#!/bin/sh

if [ -f /ram/ntpdate.started ] ; then
    exit 0
fi
touch /ram/ntpdate.started

REPTIME=20
source /etc/utils/shell-utils.sh

start_ntp_poll()
{
    while true
    do
      server_list=`awk '{ if( index($0,"server") == 1 ) { printf " %s",$2 }; } ' $NTPCONF`
      /sbin/ntpdate -u $server_list
      if [ "$?" == "0" ]; then
        exit 0
      fi
      sleep $REPTIME
      REPTIME=60
    done
}

start_ntp_poll &
exit 0

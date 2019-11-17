#!/bin/sh

# $1 - tmp file with data

if [ -f $1 ]; then
    cp -p /usr/local/share/app/bin/run_env.sh /ram/run_env.sh
    awk '{ i=index($0,"="); if(i > 1) { print( "set_if_changed ", substr($0,1,i-1),"\""substr($0,i+1,length($0))"\""); }  }' $1 >> /ram/run_env.sh
    /ram/run_env.sh
    echo "OK"
fi

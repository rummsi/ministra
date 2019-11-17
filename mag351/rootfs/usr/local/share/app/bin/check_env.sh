#!/bin/sh
if [ -n "$1" ] && [ -f $1 ]; then
    cp -p /usr/local/share/app/bin/test_env.sh /ram/test_env.sh
    awk '{ i=index($0,"="); if(i > 1) { print( "test_if_changed ", substr($0,1,i-1),"\""substr($0,i+1,length($0))"\""); }  }' $1 >> /ram/test_env.sh
    /ram/test_env.sh
    if [ "$?" != "0" ]; then
	echo "ERROR"
	exit 1
    fi
fi
echo "OK"

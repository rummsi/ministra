#!/bin/sh
set_if_changed() {
    tmp=`fw_printenv $1` > /dev/null 2>&1
    tmp=${tmp#$1=}
    if [ "$tmp" != "$2" ]; then
        fw_setenv "$1" "$2" > /dev/null 2>&1
        #echo "Set variable ""$1"" to ""$2"
    fi
}


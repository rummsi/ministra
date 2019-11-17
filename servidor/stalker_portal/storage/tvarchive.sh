#!/bin/bash

BASEDIR=`php $(dirname ${0})/realpath.php ${0}`

while : ; do

    result=`cd ${BASEDIR}; php ./tvarchivesync.php`

    # echo $result

    if [[ $result == "1" ]]; then
        sleep 10s;
        continue;
    fi

    sleep 5m
done

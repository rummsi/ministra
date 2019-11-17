#!/bin/sh

get_file_size() {
    size=`stat -c %s $1 2>/dev/null`
    size=$((size))
    export $2=$size
}

echo "ERROR: Not implemented."
exit 1;

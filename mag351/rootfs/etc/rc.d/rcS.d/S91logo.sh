#!/bin/sh

cd /usr/local/n/bin
if [ -f showlogo ]; then
  export LD_LIBRARY_PATH="/usr/lib:/usr/local/n/bin:/usr/local/lib:$LD_LIBRARY_PATH"
  gunzip -c /dev/mmcblk0p2 > /tmp/logo.bmp
  ./showlogo /tmp/logo.bmp &
fi


#!/bin/sh

. /etc/init.d/shell-utils.sh

PARTITION="/dev/block/by-name/logo"
FILE_BMP="/tmp/logo.bmp"

cd /usr/local/n/bin
if [ -f showlogo ]; then
  export LD_LIBRARY_PATH="/usr/lib:/usr/local/n/bin:/usr/local/lib:$LD_LIBRARY_PATH"
  get_flash_type ${PARTITION} type
  if [ $type == "nand" ]; then
    nanddump ${PARTITION} 2>/dev/null | gunzip > ${FILE_BMP}
  else
    gunzip -c ${PARTITION} > ${FILE_BMP}
  fi
  ./showlogo ${FILE_BMP} &
fi


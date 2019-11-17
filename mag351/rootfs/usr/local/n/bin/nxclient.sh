#!/bin/sh
# this script is used to run nexus clients.
# the server should already be started and drivers installed.

# if not already, extend LD_LIBRARY_PATH and PATH to start with the current directory
if [[ ! ${LD_LIBRARY_PATH} == .:* ]]; then
export LD_LIBRARY_PATH=.:${LD_LIBRARY_PATH}
fi
if [[ ! ${PATH} == .:* ]]; then
export PATH=.:${PATH}
fi

# usermode lxc requires client to create node
if [ ! -e /dev/brcm0 ]; then
mknod -m a=rw /dev/brcm0 c 30 0
fi

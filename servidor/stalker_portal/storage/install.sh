#!/bin/bash

BASEDIR=`php $(dirname ${0})/realpath.php ${0}`

touch .tasks
chmod 666 .tasks

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
composer install

IS_UPSTART=$(test -x /sbin/initctl && /sbin/initctl --version | grep -q upstart && dpkg --compare-versions $(lsb_release -r -s) "lt" "15.0" || echo 1)

confFile="./src/tvarchivetasks.docker.conf"
if [[ ! -f $confFile ]]; then
    confFile="./src/tvarchivetasks.conf"
fi

if [[ ${IS_UPSTART} -eq 0 ]]; then
    # upstart
    sed "s%@STORAGE_PATH@%$BASEDIR%" $confFile > /etc/init/tvarchivetasks.conf
    start tvarchivetasks
else
    # systemd
    sed "s%@STORAGE_PATH@%$BASEDIR%" ./src/tvarchivetasks.service > /etc/systemd/system/tvarchivetasks.service
    systemctl enable tvarchivetasks.service
    systemctl stop tvarchivetasks.service
    systemctl start tvarchivetasks.service >/dev/null &
fi

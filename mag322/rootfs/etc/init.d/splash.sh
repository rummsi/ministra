#!/bin/sh
. /etc/init.d/splash-utils.sh

if [ -f ${NO_SPLASH} ] ; then
    exit 0
fi
#usleep 100000

case $1 in
    log)
        echo "LOG;$2">${SPLASH_PIPE}
    ;;

    clear_log)
        echo "CLRLOG;">${SPLASH_PIPE}
    ;;

    prg)
        echo "PROGRESS;$2">${SPLASH_PIPE}
    ;;

    clear_prg)
        echo "CLRPROGRESS;">${SPLASH_PIPE}
    ;;

    menu_on)
        echo "MENUON;">${SPLASH_PIPE}
    ;;

    menu_off)
        echo "MENUOFF;">${SPLASH_PIPE}
    ;;

    show)
        echo "SHOWMENU;">${SPLASH_PIPE}
    ;;

    hide)
        echo "HIDEMENU;">${SPLASH_PIPE}
    ;;

    esc)
        echo "QUIT">${SPLASH_PIPE}
    ;;
esac

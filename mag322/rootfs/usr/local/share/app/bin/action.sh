#!/bin/sh

. /etc/init.d/shell-utils.sh

#$1 - path; $2 - return value
check_file_path()
{
    DIR1="/ram/media"
    DIR2="/ram/mnt"
    fpath="$1"
    fpath=${fpath%/*}
    rpath=`realpath "$fpath/" 2>/dev/null`
    dir1=${rpath#${DIR1}*}
    dir2=${rpath#${DIR2}*}

    if [ "$rpath" == "" ] ; then
      export $2="Error"
      return;
    fi
    if [ "$dir1" == "$rpath" ] ; then
    if [ "$dir2" == "$rpath" ] ; then
      export $2="Error"
      return;
    fi
    fi
    export $2="Ok"
}

PATH=$PATH:/home/default
case "$1" in
    reboot)
      #shutdown -r now
      reboot
    ;;
    bright)
    ;;
    mount_dir)
    check_file_path "$3" ret
    if [ "$ret" == "Error" ]; then
      echo -ne "Error: wrong mount point"
        exit 2;
      fi
      mount -t nfs -o ro,rsize=4096,wsize=4096,nolock,udp "$2" "$3"
    ;;
    umount_dir)
      umount "$2"
    ;;
    make_dir)
      mkdir "$2"
    ;;
    SetVideoOut)
    ;;
    RebootDHCP)
      set_fw_env update_from_dhcp on
      #shutdown -r now
      reboot
    ;;
    UpdateSW)
      #echo "action.sh::UpdateSW $2" > /root/action.sh.log
      if [ "$2" == "up_mc" ]; then
        set_fw_env update_from_mc on
      elif [ "$2" == "flash" ]; then
        set_fw_env active_bank 0
      else
        set_fw_env active_bank 1
      fi
      #shutdown -r now
      reboot
    ;;
    front_panel)
      #/home/default/setFpanel.sh "$2" "$3"
      ;;
    tvsystem)
      mode=""
      case "$2" in
        PAL|576p-50|720p-50|1080i-50|NTSC|576p-60|720p-60|1080i-60|1080p-50|1080p-60)
        mode="$2"
      ;;
      576i)
        mode="PAL"
      ;;
      480i)
        mode="NTSC"
      ;;
      esac
    if [ -n "$mode" ] ; then
      set_fw_env tvsystem $mode
    fi
    ;;
    graphicres)
      case "$2" in
        1920|1280|720|tvsystem_res)
        set_fw_env graphicres $2
      ;;
      esac
    ;;
    timezone)
      TZCONF="/ram/timezone"
      LTCONF="/ram/localtime"
      timezone=$2
      get_fw_env "timezone_conf" tzconf
      if [ "$tzconf" != "$timezone" ]; then
        set_fw_env "timezone_conf" $timezone
      fi
      if [ -n "$timezone" ]; then
        echo "$timezone" > $TZCONF
        rm $LTCONF
        ln -s /usr/share/zoneinfo/$timezone $LTCONF
      else
        echo "Europe/Kiev" > $TZCONF
        rm $LTCONF
        ln -s /usr/share/zoneinfo/Europe/Kiev $LTCONF
      fi
    ;;
    check_dhcp_update)
      /usr/local/share/app/bin/check_dhcp_update.sh
    ;;
    *)
      echo Bad Action code.
    ;;
esac

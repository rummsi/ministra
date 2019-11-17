#!/bin/sh
# /usr/bin/update_img.sh $upd_ver $upd_url $upd_mode
echo -n "/usr/local/share/app/bin/update_img.sh $@"

# Section 1: Check file imageupdate present and his version is correct
tmpfile=/ram/short_img
max=1000

if [ "$2" == "" ]; then
  exit 1;
fi

up_url=$2
rm -f $tmpfile 2>/dev/null 1>/dev/null

URL_HTTP=`echo $up_url |awk '{ if($1~/^http:\/\//) {print $1; exit; } }'`
TFTP_IP=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,1,i-1); exit; } }'`
TFTP_ROOTPATH=`echo $up_url |awk '{ if($1~/^tftp:\/\//) { tmp = substr($1,8); i=index(tmp,"/"); print substr(tmp,i+1); exit; } }'`
mcip_img=`echo $up_url |awk '{ if($1~/^igmp:\/\//) { tmp = substr($1,8); i=index(tmp,":"); print substr(tmp,1,i-1); exit; } }'`

if [ "$mcip_img" == "" ]; then
    if [ "$URL_HTTP" == "" ]; then
      if [ "$TFTP_IP" == "" ] || [ "$TFTP_ROOTPATH" == "" ]; then
        exit 1;
      else
        tftp -l /proc/self/fd/1 -g -r $TFTP_ROOTPATH $TFTP_IP 2>/dev/null | dd of=$tmpfile bs=1 count=$max 2>/dev/null 1>/dev/null
      fi
    else
      wget $URL_HTTP  -O /dev/stdout 2>/dev/null | dd of=$tmpfile bs=1 count=$max 2>/dev/null 1>/dev/null
    fi

    if [ ! -e $tmpfile ]; then
      echo -n "Update_Img:File absent"	
      exit 1
    fi

    img_version=`awk '{if(index($0,"Image Version:") == 1) {print substr($0,length("Image Version:")+1,length($0)); exit; }  }' $tmpfile`

    rm $tmpfile

    if [ -z "$img_version" ]; then
        exit 1
    fi
    let img_version+=0
    if [ "$?" -ne "0" ]; then
        echo -n "Update_Img: Image Version not number"
        exit 1
    fi
    if [ "$1" !=  "$img_version" ]; then
        echo -n "Update_Img: Image Version($img_version) not equal to request version($1)"
        exit 1
    fi
fi

# Section 2: Setup parametrs and check path for BootStrap and restart
# assume that we always have bootstrap(initramfs kernel) since we are already here,
# so we do not need to run bootstrap in some other way.
# So we ignore update_mode
fw_setenv update_from_dhcp on
/usr/local/share/app/bin/action.sh reboot

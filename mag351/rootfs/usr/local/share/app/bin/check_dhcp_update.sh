#!/bin/sh

#upd_sboot=`cat /ram/dhcp_ready | grep "upd_sboot="`
#upd_sboot=${upd_sboot%%#*}
#upd_sboot=${upd_sboot#upd_sboot=}

#upd_sb_ver=`cat /ram/dhcp_ready | grep "upd_sb_ver="`
#upd_sb_ver=${upd_sb_ver%%#*}
#upd_sb_ver=${upd_sb_ver#upd_sb_ver=}

#if [ -n "$upd_sboot" ]; then
#   /usr/local/share/app/bin/update_second_boot.sh $upd_sboot $upd_sb_ver
#fi

upd_ver=`cat /ram/dhcp_ready | grep "upd_ver="`
upd_ver=${upd_ver%%#*}
upd_ver=${upd_ver#upd_ver=}
upd_ver=`echo $upd_ver`

upd_url=`cat /ram/dhcp_ready | grep "upd_url="`
upd_url=${upd_url%%#*}
upd_url=${upd_url#upd_url=}

upd_mode=`cat /ram/dhcp_ready | grep "upd_mode="`
upd_mode=${upd_mode%%#*}
upd_mode=${upd_mode#upd_mode=}

if [ -n "$upd_ver" ]; then
    echo "The update number version: $upd_ver"
    img_version_now=`fw_printenv Image_Version 2>/dev/null`
    img_version_now=${img_version_now#Image_Version=}
    if [ "$upd_ver" == "$img_version_now" ]; then
        echo "The number version's equal"
    else
        # We need update
        /usr/local/share/app/bin/update_img.sh $upd_ver $upd_url $upd_mode
    fi
fi


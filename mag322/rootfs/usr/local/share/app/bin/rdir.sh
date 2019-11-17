#!/bin/sh

. /etc/init.d/shell-utils.sh

do_file_op()
{
    DIR="$1" # Directory
    OPERATION="$2" # operation: read, write, delete
    FILENAME="$3" # filename
    WRITE_DATA="$4" # data to write

    if [ "$OPERATION" = "" -o "$DIR" = "" -o "$FILENAME" = "" ]
    then
        echo "Usage: $0 [cmd] [read|write|delete] [filename] [data_to_write]"
        exit 2
    fi

    if [ ! -d "$DIR" ]
    then
        mkdir -p "$DIR" 2>/dev/null
    fi

    fpath="$DIR/$FILENAME"
    fpath=${fpath%/*}
    rpath=`realpath "$fpath/" 2>/dev/null`
    dir=${rpath#${DIR}*}

    if [ "$rpath" == "" ] ;
    then
        echo "Folder $fpath does not exist"
        exit 2
    fi
    if [ "$dir" == "$rpath" ] ;
    then
        echo "File $DIR/$FILENAME is outside $DIR folder"
        exit 2
    fi

    case "$OPERATION" in
        read)
            cat "$DIR/$FILENAME" 2>/dev/null
        ;;
        write)
            echo "$WRITE_DATA" > "$DIR/$FILENAME"
        ;;
        delete)
            rm "$DIR/$FILENAME" 2>/dev/null
        ;;
    esac
}
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

    get_hdd_info)
        /usr/bin/get_hdd_info.sh $2
    ;;
    get_storage_info)
        /usr/local/share/app/bin/get_storage_info.sh $2
    ;;
    ifconfig)
        ifconfig eth0
    ;;
    GetFile)
        check_file_path "$2" ret
        if [ "$ret" == "Error" ]; then
            exit 2;
        fi
        cat "$2" 2>/dev/null
    ;;
    RemoveFile)
        check_file_path "$2" ret
        if [ "$ret" == "Error" ]; then
            echo -ne "Error"
            exit 2;
        fi
        rm "$2" 2>/dev/null
        if [ "$?" == "0" ] ; then
            echo -ne "Ok"
        else
            echo -ne "Error"
        fi
    ;;
    RemoveDir)
        # deprecated
        DIR="/ram/media"
        fpath="$2"
        rpath=`realpath "$fpath/" 2>/dev/null`
        dir=${rpath#${DIR}*}

        if [ "$rpath" == "" ] ; then
            echo -ne "Error"
            exit 1;
        fi
        if [ "$dir" == "$rpath" ] ; then
            echo -ne "Error"
            exit 1;
        fi
        if [ "$rpath" == "/ram/media/UPnP" ] ; then
            echo -ne "Error: can't remove /media/UPnP"
            exit 2;
        fi

        subdir=${rpath#/ram/media/*}
        subdir2=${subdir%*/*}
        if [ "$subdir" != "$subdir2" ] ; then
            echo -ne "Error"
            exit 1;
        fi

        rmdir "$2" 2>/dev/null
        if [ "$?" == "0" ] ; then
            echo -ne "Ok"
        else
            echo -ne "Error: can't remove folder"
        fi
    ;;
    RemoveDirFull)
        DIR1="/ram/media/USB-"
        DIR2="/ram/media/HDD-"
        fpath="$2"
        rpath=`realpath "$fpath/" 2>/dev/null`
        dir1=${rpath#${DIR1}*}
        dir2=${rpath#${DIR2}*}

        if [ "$rpath" == "" ] ; then
            echo -ne "Error: empty path"
            exit 1;
        fi
        if [ "$dir1" == "$rpath" -a "$dir2" == "$rpath" ] ; then
            echo -ne "Error: wrong path"
            exit 1;
        fi

        rm -rf "$rpath" 2>/dev/null
        #echo "!!!!!!"
        if [ "$?" == "0" ] ; then
            echo -ne "Ok"
        else
            echo -ne "Error: can't remove folder"
            exit 1;
        fi
    ;;
    rdir)
        allp=$*
        echo ${allp##RDir }
        echo 'var dirs = ['
        ls -pL "$2" | grep "/$" | sort | awk '{print("\""$0"\",")}' 
        echo "\"\""
        echo ']'
        echo 'var files = ['
        ls -plaL "$2" | grep ".mp3$\|.wav$\|.mpg$\|.vob$\|.mp4$\|.ogg$\|.mp3\*$\|.wav\*$\|.mpg\*$\|.mp4\*$\|.mov$\|.wmv$\|.mclist$\|.m3u$\|.ogg\*$\|.gif$\|.jpg$\|.txt$\|.avi$\|.mkv$\|.MKV$\|.AVI$\|.ts$" | sort | awk '{offset=index($0,$6);  print "{\"name\" : \""substr($0,offset+13)"\", \"size\" :"$5"},"}'
        echo "{}"
        echo ']'
    ;;
    SerialNumber)
        #return STB serial number
        get_serial name
        echo -n $name
    ;;

    MACAddress)
        #return STB MAC Address
        get_eth_mac name
        echo -n $name
    ;;

    IPAddress)
        #return STB IP Address
        ifconfig eth0 | awk ' { if($1 == "inet") { print substr($2,6); exit} }'
    ;;

    HardwareVersion)
        #return STB Hardware Version
        get_hwver name
        echo -n $name
    ;;

    Vendor)
        #return STB vendor name
        get_vendor name
        echo -n $name
    ;;
    Model)
        #return STB model name
        get_model model
        #if [ "$model" == "XXXXX" ]; then
        #    get_hwver name
        #    if [ "${name:4:1}" == "Y" ]; then
        #        model="YYYYY"
        #    fi
        #fi
        echo -n $model
    ;;
    ModelExt)
        #return STB model name
        get_model model
        echo -n $model
    ;;
    ImageVersion)
        #return Nand Image Version
        get_fw_env "Image_Version" name
        echo -n $name
    ;;

    ImageDescription)
        #return Nand Image Description
        get_fw_env "Image_Desc" name
        echo -n $name
    ;;

    ImageDate)
        #return Nand Image Creation Date
        get_fw_env "Image_Date" name
        echo -n $name
    ;;
    StbAppStarted)
        if [ -f /tmp/logo.flag ]; then
            rm /tmp/logo.flag 2>/dev/null
            rm /tmp/logo.bmp  2>/dev/null
        fi
    ;;
    vmode)
        mode=`cat /etc/stbvmode | grep mode`
        mode=${mode#mode =}
        mode=${mode## }
        mode=${mode%% }
        mode="720p";#TODO!!!
        echo -ne $mode
    ;;
    gmode)
        echo -ne "1280"
    ;;
    setenv)
        shift 1
        set_fw_env $@
    ;;
    getenv)
        get_fw_env $2 val
        echo -ne $val
    ;;
    getenv2)
        get_fw_env $2 val
        echo $val
    ;;
    Img_Ver)
        #return Release Version
        cat /Img_Ver.txt
    ;;
    SetLogo)
        /usr/local/share/app/bin/set_logo.sh $2
    ;;
    UpdateSecondBoot)
        # $2  - file
        /usr/local/share/app/bin/set_second_boot.sh $2
    ;;
    ResolveIP)
        val=`nslookup $2 2>/dev/null |awk 'BEGIN{i=0;} { if($1 == "Name:"){ i=1 }; if(($1=="Address") && ($2=="1:") && i) { print $3; exit; } }'`
        echo -ne $val
    ;;
    GetCurrentBank)
        val=`awk '{ if(index($0, "root=ubi0_0 ") != 0) { printf ("RootFs"); exit; } else if (index($0, "root=ubi0_1 ") != 0) { printf ("RootFs2"); exit; } else { printf ("NFS"); exit; } }' /proc/cmdline`
        echo -ne $val
    ;;

    IPMask)
        #return STB IP Mask
        ifconfig eth0 | awk ' { if($1 == "inet") { print substr($4,6); exit} }'
    ;;

    DefaultGW)
        #return STB default gateway
        /bin/ip route list | awk '{ if ($1 == "default") { print $3; exit } }'
    ;;

    DNSServers)
        #return STB DNS servers
        awk '{ if ($1 == "nameserver") { print $2 } }' < /etc/resolv.conf
    ;;

    tempfile)
        do_file_op "/ram/data" "$2" "$3" "$4"
    ;;

    permfile)
        do_file_op "/mnt/Userfs/data" "$2" "$3" "$4"
    ;;
    LAN_link)
        cnt=`mii-tool eth0 | grep "link ok" | wc -l`
        if [ "$cnt" == "1" ] ; then
            echo -e -n "On"
        else
            echo -e -n "Off"
        fi
    ;;
    WiFi_link)
        status=`/etc/init.d/wifi.sh conn_status 2>/dev/null`
        if [ "$status" == "Connected" ] ; then
            echo -e -n "On"
        else
            echo -e -n "Off"
        fi
    ;;
    WiFi_ip)
        /etc/init.d/wifi.sh get_ip 2>/dev/null
    ;;
    SHA1)
        echo -e -n "$2" | /bin/sha1sum | cut -f1 -d " "
    ;;
    mount)
    #$2 - type; $3 - mount url; $4 - mount point; $5 - options if needed
        check_file_path "$4" ret
        if [ "$ret" == "Error" ]; then
            echo -ne "Error: wrong mount point"
            exit 2;
        fi
        cifs_opts=""
        case "$2" in
            nfs)
            ;;
            cifs)
                cifs_opts="nounix,noserverino,"
            ;;
            *)
                echo -ne "Error: bad filesystem type"
                exit 2;
            ;;
        esac
        ret=`awk '{if($2 == "'$4'" || $2 == "/ram'$4'"){printf ("Ok")}}' /etc/mtab`
        if [ "$ret" == "Ok" ] ; then
            echo -ne "Ok"
            exit
        fi

        if [ "$5" != "" ]; then
            mount -t $2 -o $cifs_opts"$5" "$3" "$4" 2>/dev/null
        else
            mount -t $2  "$3" "$4" 2>/dev/null
        fi

        if [ "$?" == "0" ] ; then
            echo -ne "Ok"
        else
            echo -ne "Error: mount failed"
        fi
    ;;
    hdd_format)
        echo {"percent":"0","state":"'inprogress'","stage":"'parted'"} > /ram/data/hdd_progress
        if [ "$3" == "" ] ; then
                /usr/bin/part_all $2 > /dev/null &
        else
                if [ "$2" == "ext2" ] ; then
                #/usr/bin/part_ext2.sh $3 > /dev/null &
                /usr/bin/part_ext2_ext3  ext2 $3 > /dev/null &
                fi
                if [ "$2" == "ext3" ] ; then
                #/usr/bin/part_ext3.sh $3 > /dev/null &
                /usr/bin/part_ext2_ext3  ext3 $3 > /dev/null &
                fi
                if [ "$2" == "ntfs" ] ; then
                #/usr/bin/part_ntfs.sh $3 > /dev/null &
                /usr/bin/part_ext2_ext3  ntfs $3 > /dev/null &
                fi
                if [ "$2" == "fat16" ] ; then
                /usr/bin/part_fat16.sh $3 > /dev/null &
                fi
                if [ "$2" == "fat32" ] ; then
                /usr/bin/part_fat32.sh $3 > /dev/null &
                fi
        fi
    ;;
    arescrypt)
        rmmod arescrypt
        sleep 1
        insmod /lib/modules/arescrypt.ko
    ;;
    arescam)
        killall arescam
        sleep 1
        shift
        /usr/bin/arescam $@
        echo "$?"
    ;;
    dhcp_is_ready)
        if [ -f /ram/dhcp_ready ] ; then
            echo -ne "1"
        else
            echo -ne "0"
        fi
    ;;
    get_dhcp_params)
        cat /ram/dhcp_ready 2>/dev/null | grep  = | sed "s/[ ]*#.*$//g"
    ;;
    GetMemoryInfo)
        cat /proc/meminfo 2>/dev/null
    ;;
    mtr)
        arglist="$2"
        while shift && [ -n "$2" ]; do
            arglist="${arglist} $2"
        done
        mtr $arglist
    ;;
    ReceiveStart)
        if [ "`ps fax | grep /usr/bin/receive | grep -v grep`" != "" ]; then
            echo -ne "OK"
        else
            /usr/bin/receive -d -h 18883 -l /dev/null 2>/dev/null >/dev/null
            [ "$?" == "0" ] && echo -ne "OK" || echo -ne "ERROR"
        fi
    ;;
    ReceiveStop)
        killall -9 receive 2>/dev/null >/dev/null
        [ "$?" == "0" ] && echo -ne "OK" || echo -ne "ERROR"
    ;;
    *) # echo Bad Action code.
        allp=$*
        echo ${allp##RDir }
        echo 'var dirs = ['
        ls -pL "$1" | grep "/$" | sort | awk '{print("\""$0"\",")}' 
        echo "\"\""
        echo ']'
        echo 'var files = ['
        ls -plaL "$1" | grep ".mp3$\|.wav$\|.mpg$\|.vob$\|.mp4$\|.ogg$\|.mp3\*$\|.wav\*$\|.mpg\*$\|.mp4\*$\|.mov$\|.wmv$\|.mclist$\|.m3u$\|.ogg\*$\|.gif$\|.jpg$\|.txt$\|.avi$\|.mkv$\|.MKV$\|.AVI$\|.ts$" | sort | awk '{offset=index($0,$6);  print "{\"name\" : \""substr($0,offset+13)"\", \"size\" :"$5"},"}'
        echo "{}"
        echo ']'
    ;;

esac



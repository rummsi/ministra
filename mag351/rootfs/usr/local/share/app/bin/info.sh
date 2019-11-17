#!/bin/sh

RDIR_APP=/usr/local/share/app/bin/rdir.sh

sn=`$RDIR_APP SerialNumber`
echo "Serial number   :"$sn
vendor=`$RDIR_APP Vendor`
echo "Vendor          :"$vendor
model=`$RDIR_APP Model`
echo "Model           :"$model
modelExt=`$RDIR_APP ModelExt`
echo "ModelExt        :"$modelExt
verhd=`$RDIR_APP HardwareVersion`
echo "Version Hardware:"$verhd
MAC=`fw_printenv ethaddr 2>/dev/null | awk ' { if( index($0,"ethaddr=") ) { print substr($0,9) } }'`
echo "MAC             :"$MAC
ip=`ifconfig eth0 | awk ' { if($1 == "inet") { print substr($2,6); exit} }'`
echo "IP              :"$ip
Image_Ver=`fw_printenv Image_Version 2>/dev/null | awk ' { if( index($0,"Image_Version=") ) { print substr($0,15) } }'`
echo "Image_Version   :"$Image_Ver
Image_Date=`fw_printenv Image_Date 2>/dev/null | awk ' { if( index($0,"Image_Date=") ) { print substr($0,12) } }'`
echo "Image_Date      :"$Image_Date
Image_Desc=`fw_printenv Image_Desc 2>/dev/null | awk ' { if( index($0,"Image_Desc=") ) { print substr($0,12) } }'`
echo "Image_Desc      :"$Image_Desc
Cur_Ver=`cat /Img_Ver.txt | awk ' { if( index($0,"ImageVersion:") ) { print substr($0,14) } }'`
echo "Current_Version :"$Cur_Ver

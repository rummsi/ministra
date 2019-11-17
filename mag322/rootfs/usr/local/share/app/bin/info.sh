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
MAC=`$RDIR_APP MACAddress`
echo "MAC             :"$MAC
ip=`$RDIR_APP IPAddress`
echo "IP              :"$ip
Image_Ver=`$RDIR_APP ImageVersion`
echo "Image_Version   :"$Image_Ver
Image_Date=`$RDIR_APP ImageDate`
echo "Image_Date      :"$Image_Date
Image_Desc=`$RDIR_APP ImageDescription`
echo "Image_Desc      :"$Image_Desc
Cur_Ver=`cat /Img_Ver.txt | awk ' { if( index($0,"ImageVersion:") ) { print substr($0,14) } }'`
echo "Current_Version :"$Cur_Ver

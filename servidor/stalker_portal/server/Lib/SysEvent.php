<?php

namespace Ministra\Lib;

class SysEvent extends \Ministra\Lib\Event
{
    public function sendMsg($msg, $header = '')
    {
        $this->setEvent('send_msg');
        $this->setNeedConfirm(1);
        $this->setMsg($msg);
        $this->setHeader($header);
        $this->send();
    }
    public function sendMsgWithVideo($msg, $video_url, $header = '')
    {
        $this->setEvent('send_msg_with_video');
        $this->setNeedConfirm(1);
        $this->setMsg($msg);
        $this->setParam1($video_url);
        $this->setHeader($header);
        $this->send();
    }
    public function sendMsgWithUrl($msg, $url, $header = '')
    {
        $this->setEvent('send_msg_with_url');
        $this->setNeedConfirm(1);
        $this->setMsg($msg);
        $this->setParam1($url);
        $this->setHeader($header);
        $this->send();
    }
    public function sendMsgAndReboot($msg, $header = '')
    {
        $this->setEvent('send_msg');
        $this->setNeedConfirm(1);
        $this->setMsg($msg);
        $this->setHeader($header);
        $this->setRebootAfterOk(1);
        $this->send();
    }
    public function sendUpdateSubscription()
    {
        $this->sendUpdateChannels();
    }
    public function sendUpdateChannels()
    {
        $this->setEvent('update_subscription');
        $this->send();
    }
    public function sendUpdateEpg()
    {
        $this->setEvent('update_epg');
        $this->send();
    }
    public function sendUpdateModules()
    {
        $this->setEvent('update_modules');
        $this->send();
    }
    public function sendMountAllStorages()
    {
        $this->setEvent('mount_all_storages');
        $master = new \Ministra\Lib\VideoMaster();
        $this->setMsg(\json_encode($master->getStoragesForStb()));
        $this->send();
    }
    public function sendPlayChannel($ch_num)
    {
        $this->setEvent('play_channel');
        $this->setMsg($ch_num);
        $this->send();
    }
    public function sendPlayRadioChannel($ch_num)
    {
        $this->setEvent('play_radio_channel');
        $this->setMsg($ch_num);
        $this->send();
    }
    public function sendCutOff()
    {
        $this->setEvent('cut_off');
        $this->send();
    }
    public function sendCutOn()
    {
        $this->setEvent('cut_on');
        $this->send();
    }
    public function sendResetPaused()
    {
        $this->sendShowMenu();
    }
    public function sendShowMenu()
    {
        $this->setEvent('show_menu');
        $this->send();
    }
    public function sendReboot()
    {
        $this->setEvent('reboot');
        $this->send();
    }
    public function sendReloadPortal()
    {
        $this->setEvent('reload_portal');
        $this->send();
    }
    public function sendAdditionalServicesStatus($status = 1)
    {
        $this->setEvent('additional_services_status');
        $this->setMsg($status);
        $this->send();
    }
    public function sendUpdatedPlaces($place = 'vclub')
    {
        $this->setEvent('updated_places');
        $this->setMsg($place);
        $this->send();
    }
    public function sendUpdateImage()
    {
        $this->setEvent('update_image');
        $this->send();
    }
    public function setPostFunctionParam($post_func, $param1)
    {
        $this->setPostFunction($post_func);
        $this->setParam1($param1);
    }
}

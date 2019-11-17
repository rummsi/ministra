<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class SimpleOssWrapper implements \Ministra\Lib\OssWrapperInterface
{
    public function getUserInfo(\Ministra\Lib\User $user)
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('oss_url')) {
            return false;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url') == '') {
            return false;
        }
        $data = \file_get_contents(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url') . (\strpos(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url'), '?') > 0 ? '&' : '?') . 'mac=' . $user->getMac() . '&serial_number=' . $user->getSerialNumber() . '&type=' . $user->getStbType() . '&locale=' . $user->getLocale() . '&login=' . $user->getLogin() . '&portal=' . (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST']) . '&verified=' . (int) $user->isVerified() . '&ip=' . $user->getIp());
        return $this->parseResult($data, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('strict_oss_url_check', true));
    }
    private function parseResult($data, $strict_check)
    {
        if (!$data) {
            return $strict_check ? ['status' => 0] : false;
        }
        $data = \json_decode($data, true);
        if (empty($data)) {
            return $strict_check ? ['status' => 0] : false;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::$debug) {
            \var_dump($data);
        }
        if ($data['status'] != 'OK' && empty($data['results'])) {
            return $strict_check ? ['status' => 0] : false;
        }
        if (\is_array($data['results']) && \array_key_exists(0, $data['results'])) {
            $info = $data['results'][0];
        } else {
            $info = $data['results'];
        }
        return $info;
    }
    public function registerSTB($mac, $serial_number, $model)
    {
        return true;
    }
    public function getPackagePrice($ext_package_id, $package_id)
    {
        return (float) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first('price');
    }
    public function subscribeToPackage($ext_package_id)
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('on_subscribe_hook_url')) {
            return true;
        }
        return $this->onSubscriptionHookResult('on_subscribe_hook_url', $ext_package_id);
    }
    private function onSubscriptionHookResult($config_param, $ext_package_id)
    {
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($config_param) == '') {
            return false;
        }
        $url = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($config_param) . '?mac=' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->mac . '&tariff_id=' . \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id)->getExternalTariffId() . '&package_id=' . $ext_package_id;
        \var_dump($url);
        $data = \file_get_contents($url);
        if (!$data) {
            throw new \Ministra\Lib\OssFault('Server error, no data');
        }
        $data = \json_decode($data, true);
        if (empty($data)) {
            throw new \Ministra\Lib\OssFault('Server error, wrong format');
        }
        \var_dump($data);
        if ($data['status'] != 'OK' && !empty($data['error'])) {
            throw new \Ministra\Lib\OssDeny($data['error']);
        }
        if ($data['status'] != 'OK' || empty($data['results'])) {
            throw new \Ministra\Lib\OssError('Server error or empty results');
        }
        return $data['results'];
    }
    public function unsubscribeFromPackage($ext_package_id)
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('on_unsubscribe_hook_url')) {
            return true;
        }
        return $this->onSubscriptionHookResult('on_unsubscribe_hook_url', $ext_package_id);
    }
    public function authorize($login, $password, $mac)
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('oss_url')) {
            return false;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url') == '') {
            return false;
        }
        $data = \file_get_contents(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url') . (\strpos(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_url'), '?') > 0 ? '&' : '?') . 'login=' . $login . '&password=' . $password . '&portal=' . (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST']) . '&mac=' . $mac . '&ip=' . (!empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : @$_SERVER['REMOTE_ADDR']));
        return $this->parseResult($data, false);
    }
}

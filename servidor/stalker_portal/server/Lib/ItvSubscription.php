<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class ItvSubscription
{
    private static $itv_subscription = false;
    public static function getBonusChannelsIds($uid)
    {
        if (self::$itv_subscription === false) {
            self::$itv_subscription = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $uid])->get()->first();
        }
        if (empty(self::$itv_subscription)) {
            return [];
        }
        $bonus_ch = self::$itv_subscription['bonus_ch'];
        if (empty($bonus_ch)) {
            return [];
        }
        $bonus_ch_arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($bonus_ch));
        if (!\is_array($bonus_ch_arr)) {
            return [];
        }
        return $bonus_ch_arr;
    }
    public static function getSubscriptionChannelsIds($uid)
    {
        $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['id' => (int) $uid])->get()->first();
        if (empty($user)) {
            return [];
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->c6e0d92fc0ec62469764ba74feb893fa()) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0])->get()->all('id');
        }
        if (self::$itv_subscription === false) {
            self::$itv_subscription = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $uid])->get()->first();
        }
        if (empty(self::$itv_subscription)) {
            return [];
        }
        $sub_ch = self::$itv_subscription['sub_ch'];
        if (empty($sub_ch)) {
            return [];
        }
        $sub_ch_arr = @\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($sub_ch));
        if (!\is_array($sub_ch_arr)) {
            return [];
        }
        return $sub_ch_arr;
    }
    public static function updateByUids($uids, $data)
    {
        if (empty($data)) {
            return false;
        }
        if (!\array_key_exists('bonus_ch', $data) || !\is_array($data['bonus_ch'])) {
            $data['bonus_ch'] = [];
        }
        if (!\array_key_exists('sub_ch', $data) || !\is_array($data['sub_ch'])) {
            $data['sub_ch'] = [];
        }
        if (\array_key_exists('sub_ch', $data)) {
            $data['sub_ch'] = \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($data['sub_ch']));
        }
        if (\array_key_exists('bonus_ch', $data)) {
            $data['bonus_ch'] = \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($data['bonus_ch']));
        }
        $data['addtime'] = 'NOW()';
        $result = false;
        foreach ($uids as $uid) {
            $subscription = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $uid])->get()->first();
            if (empty($subscription)) {
                $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('itv_subscription', \array_merge($data, ['uid' => $uid]));
            } else {
                $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv_subscription', $data, ['uid' => $uid]);
            }
        }
        if (!$result) {
            return false;
        }
        return self::getByUids($uids);
    }
    public static function getByUids($uids = array())
    {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('itv_subscription.*, users.mac as mac, users.ls as ls, ' . 'users.additional_services_on as additional_services_on')->from('itv_subscription')->join('users', 'itv_subscription.uid', 'users.id', 'LEFT');
        if (!empty($uids)) {
            $result = $result->in('uid', $uids);
        }
        $result = $result->get()->all();
        $result = \array_map(function ($item) {
            $item['sub_ch'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($item['sub_ch']));
            $item['bonus_ch'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($item['bonus_ch']));
            return $item;
        }, $result);
        return $result;
    }
}

<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class AccountInfo implements \Ministra\Lib\StbApi\AccountInfo
{
    private $stb;
    public function __construct()
    {
    }
    public function getMainInfo()
    {
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $oss_info = $user->getInfoFromOSS();
        $info = ['fname' => $user->getProfileParam('fname'), 'phone' => $user->getProfileParam('phone'), 'ls' => $user->getProfileParam('ls'), 'mac' => $user->getProfileParam('mac')];
        if (\is_array($oss_info)) {
            $info = \array_merge($info, $oss_info);
        }
        $info['last_change_status'] = $user->getProfileParam('last_change_status');
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', false) && !\array_key_exists('end_date', $info)) {
            $expire_billing_date = $user->getProfileParam('expire_billing_date');
            if (\strtotime($expire_billing_date) > 0) {
                $info['end_date'] = $expire_billing_date;
            }
        }
        if (!\array_key_exists('account_balance', $info) && $user->getProfileParam('account_balance') != '') {
            $info['account_balance'] = $user->getProfileParam('account_balance');
        }
        if (\array_key_exists('end_date', $info)) {
            $end_time = \strtotime($info['end_date']);
            if ($end_time) {
                $days = \ceil(($end_time - \time()) / (24 * 3600));
                $info['end_date'] = \date(\_('end_date_format'), \strtotime($info['end_date'])) . ' (' . \sprintf(\ngettext('%d day', '%d days', $days), $days) . ')';
            }
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $info['tariff_plan'] = $user->getTariffPlanName();
        }
        return $info;
    }
    public function getPaymentInfo()
    {
        return \sprintf(\_('account_payment_info'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('ls'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('fname'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('login'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('mac'));
    }
    public function getAgreementInfo()
    {
        return \sprintf(\_('account_agreement_info'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('ls'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('fname'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('login'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('mac'));
    }
    public function getTermsInfo()
    {
        return \sprintf(\_('account_terms_info'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('ls'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('fname'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('login'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('mac'));
    }
    public function getDemoVideoParts()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('demo_part_video_url', '');
    }
    public function getUserPackages()
    {
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $packages = $user->getPackages();
        $page = (int) $_GET['p'];
        if ($page == 0) {
            $page = 1;
        }
        $sliced_packages = \array_slice($packages, ($page - 1) * 14, 14);
        $sliced_packages = \array_map(function ($package) {
            $package['optional'] = (bool) $package['optional'];
            if ($package['subscribed']) {
                $package['subscribed_str'] = \_('Subscribed');
            } else {
                $package['not_subscribed_str'] = \_('Not subscribed');
            }
            return $package;
        }, $sliced_packages);
        $data = ['total_items' => \count($packages), 'max_page_items' => 14, 'selected_item' => 0, 'cur_page' => 0, 'data' => $sliced_packages];
        return $data;
    }
}

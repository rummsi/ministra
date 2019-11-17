<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Account implements \Ministra\Lib\StbApi\Account
{
    public function subscribeToPackage()
    {
        $package_id = (int) $_REQUEST['package_id'];
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $response = [];
        try {
            $response['result'] = $user->subscribeToPackage($package_id);
        } catch (\Ministra\Lib\OssDeny $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Ministra\Lib\OssException $e) {
            $response['message'] = \_('This operation is temporarily unavailable.');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        }
        return $response;
    }
    public function unsubscribeFromPackage()
    {
        $package_id = (int) $_REQUEST['package_id'];
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $response = [];
        try {
            $response['result'] = $user->unsubscribeFromPackage($package_id);
        } catch (\Ministra\Lib\OssDeny $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Ministra\Lib\OssException $e) {
            $response['message'] = \_('This operation is temporarily unavailable.');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        }
        return $response;
    }
    public function checkPrice()
    {
        $package_id = (int) $_REQUEST['package_id'];
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $response = [];
        try {
            $response['result'] = $user->getPriceForPackage($package_id);
        } catch (\Ministra\Lib\OssDeny $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Ministra\Lib\OssException $e) {
            $response['message'] = \_('This operation is temporarily unavailable.');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        }
        return $response;
    }
    public function checkVideoPrice()
    {
        $video_id = (int) $_REQUEST['video_id'];
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $response = [];
        try {
            $package = $user->getPackageByVideoId($video_id);
            if (empty($package)) {
                throw new \Exception(\_('Server error'));
            }
            $response['result'] = $user->getPriceForPackage($package['id']);
            $response['rent_duration'] = $package['rent_duration'];
            $response['package_id'] = $package['id'];
        } catch (\Ministra\Lib\OssDeny $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Ministra\Lib\OssException $e) {
            $response['message'] = \_('This operation is temporarily unavailable.');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        }
        return $response;
    }
    public function rentVideo()
    {
        $video_id = (int) $_REQUEST['video_id'];
        $price = $_REQUEST['price'];
        $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        $response = [];
        try {
            $package = $user->getPackageByVideoId($video_id);
            if (empty($package)) {
                throw new \Exception(\_('Server error'));
            }
            if ($price === '0') {
                $oss_result = true;
            } else {
                $oss_result = $user->subscribeToPackage($package['id']);
            }
            $response['result'] = $oss_result;
            $response['rent_duration'] = $package['rent_duration'];
            $response['package_id'] = $package['id'];
            $rent_session_id = $user->rentVideo($video_id, $price);
            $response['rent_info'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_rent')->where(['id' => $rent_session_id])->get()->first();
            $response['rent_info']['expires_in'] = \Ministra\Lib\User::humanDateDiff($response['rent_info']['rent_end_date'], $response['rent_info']['rent_date']);
        } catch (\Ministra\Lib\OssDeny $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Ministra\Lib\OssException $e) {
            $response['message'] = \_('This operation is temporarily unavailable.');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
        }
        return $response;
    }
}

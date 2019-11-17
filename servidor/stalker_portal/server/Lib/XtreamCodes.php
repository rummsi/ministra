<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
class XtreamCodes
{
    public static function getHash($mac_add, $ip, $channel_id, $max_seconds)
    {
        $encrypt = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($mac_add . '=' . $ip . '=' . $channel_id . '=' . (\time() + $max_seconds));
        $iv = \mcrypt_create_iv(\mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $key = \pack('H*', \md5(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('xtream_key')));
        $mac = \hash_hmac('sha256', $encrypt, \substr(\bin2hex($key), -32));
        $passcrypt = \mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt . $mac, MCRYPT_MODE_CBC, $iv);
        $encoded = \urlencode(\base64_encode(\base64_encode($passcrypt) . '|' . \base64_encode($iv)));
        return $encoded;
    }
}

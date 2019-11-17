<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class OssWrapper
{
    private static $instance = null;
    private function __construct()
    {
    }
    public static function getWrapper()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        $wrapper_class = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('oss_wrapper');
        self::$instance = new $wrapper_class();
        return self::$instance;
    }
}

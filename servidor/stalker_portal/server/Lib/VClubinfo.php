<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class VClubinfo implements \Ministra\Lib\StbApi\VClubinfo
{
    public static function getInfoById($id, $provider = false, $type = null)
    {
        $class_name = self::getProvider($provider);
        return $class_name::getInfoById($id, $type);
    }
    private static function getProvider($provider = false)
    {
        $class = \ucfirst(!empty($provider) ? $provider : \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_info_provider', 'kinopoisk'));
        if (!\class_exists($class)) {
            throw new \Exception('Resource "' . $class . '" does not exist');
        }
        return $class;
    }
    public static function getInfoByName($orig_name, $provider = false)
    {
        $class_name = self::getProvider($provider);
        return $class_name::getInfoByName($orig_name);
    }
    public static function getRatingByName($orig_name, $provider = false)
    {
        $class_name = self::getProvider($provider);
        return $class_name::getRatingByName($orig_name);
    }
    public static function getRatingById($id, $provider = false, $type = null)
    {
        $class_name = self::getProvider($provider);
        return $class_name::getRatingById($id, $type);
    }
}

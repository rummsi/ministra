<?php

namespace Ministra\Lib\StbApi;

interface VClubinfo
{
    public static function getInfoById($id, $type = null);
    public static function getInfoByName($orig_name);
    public static function getRatingByName($orig_name);
    public static function getRatingById($id, $type = null);
}

<?php

namespace Ministra\Admin\Lib;

use Ministra\Lib\Itv as LibItv;
class Itv extends \Ministra\Lib\Itv
{
    public static function getServices()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, CONCAT_WS(". ", cast(number as char), name) as name, number')->from('itv')->orderby('number')->get()->all();
    }
}

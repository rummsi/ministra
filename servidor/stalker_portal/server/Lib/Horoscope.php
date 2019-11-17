<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class Horoscope extends \Ministra\Lib\Widget implements \Ministra\Lib\StbApi\Horoscope
{
    public $widget_name = 'horoscope';
    public $cache_expire = 3600;
    public $rss_url;
    public $rss_fields = array('title', 'description');
    public function __construct()
    {
        parent::__construct();
        $this->rss_url = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('horoscope_rss');
    }
}

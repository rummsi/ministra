<?php

namespace Ministra\Lib;

class Curweather extends \Ministra\Lib\Google
{
    public $gapi_name = 'cur_weather';
    public $gapi_module = 'weather';
    public $gapi_url = 'http://www.google.com/ig/api?hl=ru&weather=Odessa,,,46430000,30770000&oe=utf8';
    public $cache_expire = 600;
    public $gapi_field = 'current_conditions';
    public function getData()
    {
        $tmp_arr = parent::getData();
        $new_tmp_arr = $tmp_arr[0];
        return $new_tmp_arr;
    }
}

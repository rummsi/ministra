<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Gismeteo
{
    public $xml_url = 'http://informer.gismeteo.ru/xml/33837_1.xml';
    private $db;
    private $cache_table = 'gismeteo_day_weather';
    private $weekday_arr = array();
    private $tod_arr = array();
    private $month_arr = array();
    private $cloudiness_arr = array();
    private $precipitation_arr = array();
    private $direction_arr = array();
    private $precipitation_img_arr = array('', '', '', '', 'w_rain.png', 'w_rain_strong.png', 'w_snow.png', 'w_snow.png', 'w_thunderstorm.png', '', 'w_empty.png');
    private $cloudiness_img_arr = array('w_empty.png', 'w_cloud_small.png', 'w_cloud_big.png', 'w_cloud_black.png');
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->xml_url = $this->xml_url . '?' . \time();
        $this->weekday_arr = ['', \_('Sun'), \_('Mon'), \_('Tue'), \_('Wed'), \_('Thu'), \_('Fri'), \_('Sat')];
        $this->tod_arr = [\_('Night'), \_('Morning'), \_('Day'), \_('Evening')];
        $this->month_arr = ['', \_('Jan'), \_('Feb'), \_('Mar'), \_('Apr'), \_('May'), \_('Jun'), \_('Jul'), \_('Aug'), \_('Sep'), \_('Oct'), \_('Nov'), \_('Dec')];
        $this->cloudiness_arr = [\_('clear'), \_('partly cloudy'), \_('cloudy'), \_('overcast')];
        $this->precipitation_arr = ['', '', '', '', \_('rain'), \_('rainfall'), \_('snow'), \_('snow'), \_('thunderstorm'), \_('no data'), \_('w/o precipitation')];
        $this->direction_arr = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    }
    public function getData()
    {
        return $this->getDataFromDBCache();
    }
    private function getDataFromDBCache()
    {
        $content = $this->db->from($this->cache_table)->get()->first('content');
        $content = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($content));
        if (\is_array($content)) {
            $content = \array_map(function ($day) {
                $day['title'] = \_($day['tod']) . ' ' . $day['day'] . ' ' . \_($day['months']) . ', ' . \_($day['weekday']);
                $day['phenomena'] = \_($day['cloudiness']) . ', ' . \_($day['precipitation']);
                $day['wind'] = \_($day['wind_from']) . ', ' . $day['wind_min'] . '-' . $day['wind_max'];
                return $day;
            }, $content);
            return $content;
        }
        return 0;
    }
    public function getDataFromXML()
    {
        $gis_arr = [];
        $xml_resp = \simplexml_load_file($this->xml_url);
        if ($xml_resp) {
            $i = 0;
            foreach ($xml_resp->REPORT->TOWN->FORECAST as $item) {
                $tod_id = (string) $item->attributes()->tod;
                $tod = $this->tod_arr[$tod_id];
                $day = (string) $item->attributes()->day;
                $month_id = (int) $item->attributes()->month;
                $months = $this->month_arr[$month_id];
                $weekday_id = (string) $item->attributes()->weekday;
                $weekday = $this->weekday_arr[$weekday_id];
                $title = $tod . ' ' . $day . ' ' . $months . ', ' . $weekday;
                $t_min = (string) $item->TEMPERATURE->attributes()->min;
                $t_max = (string) $item->TEMPERATURE->attributes()->max;
                if ($t_min > 0) {
                    $t_min = '+' . $t_min;
                }
                if ($t_max > 0) {
                    $t_max = '+' . $t_max;
                }
                $temperature = $t_min . '..' . $t_max . '&ordm;';
                $pattern = ["/(\\d)/", '/&ordm;/', "/\\+/", "/\\-/", "/\\.\\./"];
                $replace = ["<img src='i/\\1.png'>", "<img src='i/deg.png'>", "<img src='i/plus.png'>", "<img src='i/minus.png'>", "<img src='i/dots.png'>"];
                $temperature = \preg_replace($pattern, $replace, $temperature);
                $cloudiness_id = (string) $item->PHENOMENA->attributes()->cloudiness;
                $cloudiness = $this->cloudiness_arr[$cloudiness_id];
                $precipitation_id = (string) $item->PHENOMENA->attributes()->precipitation;
                $precipitation = $this->precipitation_arr[$precipitation_id];
                $phenomena = $cloudiness . ', ' . $precipitation;
                $pressure = (string) $item->PRESSURE->attributes()->min . '..' . (string) $item->PRESSURE->attributes()->max;
                $pressure_str = $pressure . ' мм рт.ст.';
                $wind_from_id = (string) $item->WIND->attributes()->direction;
                $wind_from = $this->direction_arr[$wind_from_id];
                $wind_min = (string) $item->WIND->attributes()->min;
                $wind_max = (string) $item->WIND->attributes()->max;
                $wind = $wind_from . ', ' . $wind_min . '-' . $wind_max;
                $wind_str = $wind . ' м/с';
                $gis_arr[$i]['title'] = $title;
                $gis_arr[$i]['tod'] = $tod;
                $gis_arr[$i]['tod_id'] = $tod_id;
                $gis_arr[$i]['day'] = $day;
                $gis_arr[$i]['months'] = $months;
                $gis_arr[$i]['month_id'] = $month_id;
                $gis_arr[$i]['weekday'] = $weekday;
                $gis_arr[$i]['weekday_id'] = $weekday_id;
                $gis_arr[$i]['temperature'] = $temperature;
                $gis_arr[$i]['phenomena'] = $phenomena;
                $gis_arr[$i]['pressure'] = $pressure;
                $gis_arr[$i]['wind_from_id'] = $wind_from_id;
                $gis_arr[$i]['wind_from'] = $wind_from;
                $gis_arr[$i]['wind_min'] = $wind_min;
                $gis_arr[$i]['wind_max'] = $wind_max;
                $gis_arr[$i]['wind'] = $wind;
                $gis_arr[$i]['cloudiness'] = $cloudiness;
                $gis_arr[$i]['cloudiness_id'] = $cloudiness_id;
                $gis_arr[$i]['precipitation'] = $precipitation;
                $gis_arr[$i]['precipitation_id'] = $precipitation_id;
                $gis_arr[$i]['description'] = $phenomena . ',<br>' . \_('pressure') . ' ' . $pressure_str . ', ' . \_('wind') . ' ' . $wind_str;
                if ($tod_id == 0 || $tod_id == 3) {
                    $img_1 = 'w_moon.png';
                } else {
                    $img_1 = 'w_sun.png';
                }
                $gis_arr[$i]['img_1'] = $img_1;
                $gis_arr[$i]['img_2'] = $this->precipitation_img_arr[$precipitation_id];
                $gis_arr[$i]['img_3'] = $this->cloudiness_img_arr[$cloudiness_id];
                ++$i;
            }
            $this->setDataDBCache($gis_arr);
            return $gis_arr;
        }
    }
    private function setDataDBCache($arr)
    {
        $content = \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($arr));
        $result = $this->db->from($this->cache_table)->get();
        $crc = $result->get('crc');
        if (\md5($content) != $crc) {
            $data = ['content' => $content, 'updated' => 'NOW()', 'url' => $this->xml_url, 'crc' => \md5($content)];
            if ($result->count() == 1) {
                $this->db->update($this->cache_table, $data);
            } else {
                $this->db->insert($this->cache_table, $data);
            }
        } else {
            if ($result->count() == 1) {
                $this->db->update($this->cache_table, ['updated' => 'NOW()']);
            }
        }
    }
}

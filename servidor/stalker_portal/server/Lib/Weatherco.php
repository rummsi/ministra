<?php

namespace Ministra\Lib;

use DateTime;
use DateTimeZone;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Weatherco extends \Ministra\Lib\WeatherProvider
{
    public function __construct()
    {
        parent::__construct();
    }
    public function updateFullCurrent()
    {
        $start = \microtime(1);
        $content = \file_get_contents('http://xml.weather.ua/1.2/fullcurrent/', false, \stream_context_create($this->context_params));
        $xml_resp = \simplexml_load_string($content);
        if (!$xml_resp) {
            echo "Error loading fullcurrent weather\n";
            echo 'Time: ' . (\microtime(1) - $start) . "\n";
            foreach (\libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
            exit;
        }
        foreach ($xml_resp->current as $current) {
            $item = [];
            $item['city_id'] = (int) $current->attributes()->city;
            foreach ($current as $field => $val) {
                $item[$field] = (string) $val;
                if ($field == 'date') {
                    $item['hour'] = \date('G', \strtotime((string) $val));
                }
            }
            $item = $this->preParse($item);
            $this->setCurrentCache($item);
        }
    }
    public function updateFullForecast()
    {
        $start = \microtime(1);
        $content = \file_get_contents('http://xml.weather.ua/1.2/fullforecast/', false, \stream_context_create($this->context_params));
        $xml_resp = \simplexml_load_string($content);
        if (!$xml_resp) {
            echo "Error loading fullforecast weather\n";
            echo 'Downloaded in: ' . (\microtime(1) - $start) . "\n";
            echo 'Time: ' . (\microtime(1) - $start) . "\n";
            foreach (\libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
            exit;
        }
        foreach ($xml_resp->forecast as $forecast) {
            $weather = [];
            $weather['city_id'] = (int) $forecast->attributes()->city;
            $weather['forecast'] = [];
            foreach ($forecast->day as $day) {
                $item = [];
                $item['date'] = (string) $day->attributes()->date;
                $item['hour'] = (int) $day->attributes()->hour;
                $item['timestamp'] = \strtotime($item['date'] . ' ' . $item['hour'] . ':00:00');
                foreach ($day as $field => $val) {
                    if ($val->count() > 0) {
                        $child = [];
                        foreach ($val as $child_name => $child_val) {
                            $child[$child_name] = (string) $child_val;
                        }
                        $item[$field] = $child;
                    } else {
                        $item[$field] = (string) $val;
                    }
                }
                $weather['forecast'][] = $this->preParse($item);
                $this->setForecastCache($weather);
            }
        }
    }
    public function getCurrent()
    {
        $city_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->city_id;
        if ($city_id == 0) {
            return ['error' => 'not_configured'];
        }
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        $current = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($cache['current']));
        if (!empty($current) && \is_array($current)) {
            $current['city'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cities')->where(['id' => $current['city_id']])->get()->first(\_('city_name_field'));
            $current['cloud_str'] = \_($current['cloud_str']);
            $current['w_rumb_str'] = \str_replace('/', '', $current['w_rumb_str']);
            return $this->postParse($current);
        }
        return false;
    }
    public function postParse($weather)
    {
        if (!empty($weather['date'])) {
            if (\strlen($weather['date']) == 10 && !empty($weather['hour'])) {
                $weather['date'] = $weather['date'] . ' ' . $weather['hour'] . ':00:00';
            }
            $target_timezone = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cities')->where(['id' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->city_id])->get()->first('timezone');
            if (!$target_timezone) {
                $target_timezone = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->d3faade18a2006d7cb3671f32c4256bcf();
            }
            $date = new \DateTime($weather['date'], new \DateTimeZone('Europe/Kiev'));
            $date->setTimeZone(new \DateTimeZone($target_timezone));
            $weather['date_orig'] = $weather['date'];
            $weather['date'] = $date->format('Y-m-d H:i:s');
            $weather['hour'] = $date->format('G');
            $weather['pict'] = $this->getPicture($weather);
        }
        return $weather;
    }
    public function getForecast()
    {
        $city_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->city_id;
        if ($city_id == 0) {
            return ['error' => 'not_configured'];
        }
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        $tod_arr = [3 => 'Night', 9 => 'Morning', 15 => 'Day', 21 => 'Evening'];
        $weather = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($cache['forecast']));
        $weather['city'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cities')->where(['id' => $weather['city_id']])->get()->first(\_('city_name_field'));
        if (!empty($weather) && \is_array($weather)) {
            $that = $this;
            $weather['forecast'] = \array_map(function ($day) use($tod_arr, $that) {
                $day['title'] = \_($tod_arr[$day['hour']]) . ' ' . \date('j', $day['timestamp']) . ' ' . \_(\date('M', $day['timestamp'])) . ', ' . \_(\date('D', $day['timestamp']));
                $day['cloud_str'] = \_($day['cloud_str']);
                $day['w_rumb_str'] = \str_replace('/', '', $day['w_rumb_str']);
                $day['temperature'] = ($day['t']['min'] > 0 ? '+' : '') . $day['t']['min'] . '..' . ($day['t']['max'] > 0 ? '+' : '') . $day['t']['max'] . '&deg;';
                return $that->postParse($day);
            }, $weather['forecast']);
            return $weather;
        }
        return false;
    }
    public function getCities($country_id, $search = '')
    {
        $result = [];
        if (empty($search)) {
            $cities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cities')->where(['country_id' => $country_id])->orderby('name_en')->get()->all();
            foreach ($cities as $city) {
                $selected = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->city_id == $city['id'] ? 1 : 0;
                $city_name = $city['name_en'];
                $result[] = ['label' => $city_name, 'value' => $city['id'], 'timezone' => $city['timezone'], 'selected' => $selected];
            }
        } else {
            $cities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name_en')->from('cities')->where(['country_id' => $country_id])->like(['name' => \iconv('windows-1251', 'utf-8', $search) . '%', 'name_en' => $search . '%'], 'OR ')->limit(3)->get()->all();
            $result = [];
            foreach ($cities as $city) {
                $result[] = ['label' => $city['name_en'], 'value' => $city['id']];
            }
        }
        return $result;
    }
    public function getCityFieldName()
    {
        return 'city_id';
    }
}

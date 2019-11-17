<?php

namespace Ministra\Lib;

use DateTime;
use DateTimeZone;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Openweathermap extends \Ministra\Lib\WeatherProvider
{
    protected $conditions_map = array('01' => array('cloud' => 0), '02' => array('cloud' => 10), '03' => array('cloud' => 20), '04' => array('cloud' => 30), '09' => array('cloud' => 40), '10' => array('cloud' => 50), '11' => array('cloud' => 60), '13' => array('cloud' => 90), '50' => array('cloud' => 30), '906' => array('cloud' => 70), '611' => array('cloud' => 80), '612' => array('cloud' => 80), '621' => array('cloud' => 100), '622' => array('cloud' => 100));
    private $appid = '';
    public function __construct()
    {
        parent::__construct();
        $this->appid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('openweathermap_appid', '');
        if ($this->appid) {
            $this->context_params['http']['header'] = 'x-api-key: ' . $this->appid;
        }
    }
    public function getCurrent()
    {
        $city_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->openweathermap_city_id;
        if ($city_id == 0) {
            return ['error' => 'not_configured'];
        }
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        if (empty($cache) || empty($cache['current'])) {
            $current = $this->getCurrentFromSource($city_id, $cache);
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        } elseif (empty($cache['current'])) {
            return ['repeat_time' => 10];
        } else {
            $current = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($cache['current']));
        }
        if (\time() - \strtotime($cache['last_request']) > 600) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update($this->cache_table, ['last_request' => 'NOW()'], ['id' => $cache['id']]);
        }
        if (!empty($current) && \is_array($current)) {
            $current['city'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('all_cities')->where(['id' => $current['city_id']])->get()->first(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? 'name_ru' : 'name');
            $current['cloud_str'] = \_($current['cloud_str']);
            $current['w_rumb_str'] = \str_replace('/', '', $current['w_rumb_str']);
            return $this->postParse($current);
        }
        return false;
    }
    private function getCurrentFromSource($city_id, $cache_data)
    {
        if (empty($cache_data)) {
            $cache_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert($this->cache_table, ['city_id' => $city_id])->insert_id();
            if (!$cache_id) {
                return false;
            }
        }
        return $this->updateCurrentById($city_id);
    }
    private function updateCurrentById($id)
    {
        $url = 'http://api.openweathermap.org/data/2.5/weather?id=' . $id;
        $content = \file_get_contents($url, false, \stream_context_create($this->context_params));
        $content = \json_decode($content, true);
        $weather = $this->normalizeWeatherData($content);
        if (!empty($weather)) {
            $this->setCurrentCache($weather);
        }
        return $weather;
    }
    private function normalizeWeatherData($content)
    {
        if (!$content) {
            return false;
        }
        $weather = [];
        if (isset($content['id'])) {
            $weather['city_id'] = $content['id'];
        }
        if (isset($content['dt'])) {
            $weather['dt'] = $content['dt'];
        }
        if (isset($content['weather'][0]['id']) && isset($this->conditions_map[$content['weather'][0]['id']])) {
            $weather['cloud'] = $this->conditions_map[$content['weather'][0]['id']]['cloud'];
        }
        if (!isset($content['cloud']) && isset($content['weather'][0]['icon']) && isset($this->conditions_map[\substr($content['weather'][0]['icon'], 0, 2)])) {
            $weather['cloud'] = $this->conditions_map[\substr($content['weather'][0]['icon'], 0, 2)]['cloud'];
        }
        if (!isset($content['coord']) && isset($content['main']['temp_min']) && isset($content['main']['temp_max'])) {
            $weather['t'] = ['min' => $content['main']['temp_min'], 'max' => $content['main']['temp_max']];
        } elseif (isset($content['main']['temp'])) {
            $weather['t'] = $content['main']['temp'];
        }
        if (isset($content['main']['pressure'])) {
            $weather['p'] = \ceil($content['main']['pressure'] / 1.3332239);
        }
        if (isset($content['main']['humidity'])) {
            $weather['h'] = $content['main']['humidity'];
        }
        if (isset($content['wind']['speed'])) {
            $weather['w'] = $weather['wind'] = \round($content['wind']['speed']);
        }
        if (isset($content['wind']['deg'])) {
            $weather['w_rumb'] = $content['wind']['deg'];
        }
        if (isset($content['sys']['sunrise'])) {
            $weather['sunrise'] = $content['sys']['sunrise'];
        }
        if (isset($content['sys']['sunset'])) {
            $weather['sunset'] = $content['sys']['sunset'];
        }
        return $this->preParse($weather);
    }
    public function postParse($weather)
    {
        if (!empty($weather['dt'])) {
            $target_timezone = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('all_cities')->where(['id' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->openweathermap_city_id])->get()->first('timezone');
            $date = new \DateTime('@' . $weather['dt'], new \DateTimeZone('UTC'));
            if ($target_timezone) {
                $date->setTimeZone(new \DateTimeZone($target_timezone));
            }
            $weather['date'] = $date->format('Y-m-d H:i:s');
            $weather['hour'] = $date->format('G');
            $weather['pict'] = $this->getPicture($weather);
        }
        if (!empty($weather['t'])) {
            $weather['t_units'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('units') == 'imperial' ? 'F' : 'C';
            if (!\is_array($weather['t'])) {
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('units') == 'imperial') {
                    $weather['t'] = \round($weather['t'] * 9 / 5 - 459.67);
                } else {
                    $weather['t'] = \round($weather['t'] - 273.15);
                }
            } else {
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('units') == 'imperial') {
                    $weather['t']['min'] = \round($weather['t']['min'] * 9 / 5 - 459.67);
                    $weather['t']['max'] = \round($weather['t']['max'] * 9 / 5 - 459.67);
                } else {
                    $weather['t']['min'] = \round($weather['t']['min'] - 273.15);
                    $weather['t']['max'] = \round($weather['t']['max'] - 273.15);
                }
                $weather['t_units'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('units') == 'imperial' ? 'F' : 'C';
                $weather['temperature'] = ($weather['t']['max'] > 0 ? '+' : '') . $weather['t']['max'] . '&deg; ' . $weather['t_units'];
            }
        }
        return $weather;
    }
    public function updateFullCurrent()
    {
        $city_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['last_request>' => \date('Y-m-d H:i:s', \time() - 24 * 3600)])->get()->all('city_id');
        if ($city_ids) {
            $this->updateCurrentByGroupIds($city_ids);
        }
    }
    private function updateCurrentByGroupIds($ids)
    {
        if (\count($ids) > 20) {
            $chunks = \array_chunk($ids, 20);
            foreach ($chunks as $chunk) {
                $this->updateCurrentByGroupIds($chunk);
                \sleep(20);
            }
            return;
        }
        $url = 'http://api.openweathermap.org/data/2.5/group?id=' . \implode(',', $ids);
        $content = \file_get_contents($url, false, \stream_context_create($this->context_params));
        $content = \json_decode($content, true);
        if ($content && !empty($content['list'])) {
            foreach ($content['list'] as $weather) {
                $weather = $this->normalizeWeatherData($weather);
                if (!empty($weather)) {
                    $this->setCurrentCache($weather);
                }
            }
        }
    }
    public function getForecast()
    {
        $city_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->openweathermap_city_id;
        if ($city_id == 0) {
            return ['error' => 'not_configured'];
        }
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        if (empty($cache) || empty($cache['forecast'])) {
            $weather = $this->getForecastFromSource($city_id, $cache);
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['city_id' => $city_id])->get()->first();
        } elseif (empty($cache['forecast'])) {
            return ['repeat_time' => 10];
        } else {
            $weather = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($cache['forecast']));
        }
        if (\time() - \strtotime($cache['last_request']) > 600) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update($this->cache_table, ['last_request' => 'NOW()'], ['id' => $cache['id']]);
        }
        $city = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('all_cities')->where(['id' => $city_id])->get()->first();
        $weather['city'] = $city[\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? 'name_ru' : 'name'];
        $target_timezone = $city['timezone'];
        if (!empty($weather) && \is_array($weather)) {
            $that = $this;
            $weather['forecast'] = \array_map(function ($day) use($that, $target_timezone) {
                $date = new \DateTime('@' . $day['dt'], new \DateTimeZone('UTC'));
                if ($target_timezone) {
                    $date->setTimeZone(new \DateTimeZone($target_timezone));
                }
                $day['title'] = \_($that->getDayPart($date->format('G'))) . ' ' . $date->format('j') . ' ' . \_($date->format('M')) . ', ' . \_($date->format('D'));
                $day['cloud_str'] = \_($day['cloud_str']);
                $day['w_rumb_str'] = \str_replace('/', '', $day['w_rumb_str']);
                $day['temperature'] = ($day['t']['min'] > 0 ? '+' : '') . $day['t']['min'] . '..' . ($day['t']['max'] > 0 ? '+' : '') . $day['t']['max'] . '&deg;';
                return $that->postParse($day);
            }, $weather['forecast']);
            return $weather;
        }
        return false;
    }
    private function getForecastFromSource($city_id, $cache_data)
    {
        if (empty($cache_data)) {
            $cache_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert($this->cache_table, ['city_id' => $city_id])->insert_id();
            if (!$cache_id) {
                return false;
            }
        }
        return $this->updateForecastById($city_id);
    }
    private function updateForecastById($id)
    {
        $url = 'http://api.openweathermap.org/data/2.5/forecast?id=' . $id;
        $content = \file_get_contents($url, false, \stream_context_create($this->context_params));
        $content = \json_decode($content, true);
        $weather = [];
        $weather['city_id'] = $id;
        $weather['forecast'] = [];
        if ($content && !empty($content['list'])) {
            $indexes = [];
            for ($i = 0; $i < 4; ++$i) {
                $indexes[] = $i * 2 + 1;
            }
            foreach ($indexes as $idx) {
                if (isset($content['list'][$idx])) {
                    $weather['forecast'][] = $this->normalizeWeatherData($content['list'][$idx]);
                }
            }
        }
        if (!empty($weather)) {
            $this->setForecastCache($weather);
        }
        return $weather;
    }
    public function getDayPart($hour)
    {
        if ($hour >= 6 && $hour < 12) {
            return 'Morning';
        } elseif ($hour >= 12 && $hour < 18) {
            return 'Day';
        } elseif ($hour >= 18 && $hour < 24) {
            return 'Evening';
        } elseif ($hour >= 0 && $hour < 6) {
            return 'Night';
        }
        return '';
    }
    public function updateFullForecast()
    {
        $city_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($this->cache_table)->where(['last_request>' => \date('Y-m-d H:i:s', \time() - 24 * 3600)])->get()->all('city_id');
        foreach ($city_ids as $city_id) {
            $this->updateForecastById($city_id);
        }
    }
    public function getCities($country_id, $search = '')
    {
        $result = [];
        if (empty($search)) {
            $cities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('all_cities')->where(['country_id' => $country_id])->orderby('name')->get()->all();
            foreach ($cities as $city) {
                $selected = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->openweathermap_city_id == $city['id'] ? 1 : 0;
                $result[] = ['label' => $city['name'], 'value' => $city['id'], 'timezone' => $city['timezone'], 'selected' => $selected];
            }
        } else {
            $cities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name')->from('all_cities')->where(['country_id' => $country_id])->like(['name_ru' => \iconv('windows-1251', 'utf-8', $search) . '%', 'name' => $search . '%'], 'OR ')->limit(3)->get()->all();
            $result = [];
            foreach ($cities as $city) {
                $result[] = ['label' => $city['name'], 'value' => $city['id']];
            }
        }
        return $result;
    }
    public function getCityFieldName()
    {
        return 'openweathermap_city_id';
    }
}

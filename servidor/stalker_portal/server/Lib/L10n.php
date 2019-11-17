<?php

namespace Ministra\Lib;

use ErrorException;
use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class L10n
{
    public static $api_key = 'ABQIAAAA8gol0t00IMl-GLDtPLoQnRT2RNzrSW75x_tEA63PvQHiSnPv7BQnFyZpHgybA9POm2hOwqHdf4JatA';
    public static $geonames_username = 'azhurb';
    public static function updateCitiesInfo()
    {
        return self::updateAllAvailableCountries();
    }
    private static function updateAllAvailableCountries($force = false)
    {
        if ($force) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('truncate table countries');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('truncate table cities');
        }
        $xml_resp = \simplexml_load_file('http://xml.weather.ua/1.2/country/');
        if (!$xml_resp) {
            throw new \ErrorException("Couldn't load country xml");
        }
        foreach ($xml_resp->country as $country) {
            $item = [];
            $item['id'] = (int) $country->attributes()->id;
            $db_country = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('countries')->where(['id' => $item['id']])->get()->first();
            foreach ($country as $field => $val) {
                if ($field == 'region') {
                    $item['region'] = (string) $val;
                    $item['region_id'] = (int) $val->attributes()->id;
                } else {
                    $item[$field] = (string) $val;
                }
            }
            if (empty($db_country['id'])) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('countries', $item);
            } else {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('countries', $item, ['id' => $db_country['id']]);
            }
            self::updateAllCitiesByCountryId($item['id']);
        }
    }
    private static function updateAllCitiesByCountryId($country_id)
    {
        if (empty($country_id)) {
            return;
        }
        $xml_resp = \simplexml_load_file('http://xml.weather.ua/1.2/city/?country=' . $country_id);
        if (!$xml_resp) {
            throw new \ErrorException("Couldn't load city xml for country " . $country_id);
        }
        $delay = 100000;
        foreach ($xml_resp->city as $city) {
            $geocode_pending = true;
            while ($geocode_pending) {
                $item = [];
                $item['id'] = (int) $city->attributes()->id;
                $db_city = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cities')->where(['id' => $item['id']])->get()->first();
                foreach ($city as $field => $val) {
                    $item[$field] = (string) $val;
                }
                $item['country'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('countries')->where(['id' => $item['country_id']])->get()->first('name_en');
                $geocode_pending = false;
                if (empty($db_city['timezone'])) {
                    try {
                        $item['timezone'] = self::getTimezoneForCity($item['country'], $item['name_en']);
                    } catch (\Ministra\Lib\GeoCodeException $ge) {
                        echo 'Bad status for country: ' . $item['country'] . ', city: ' . $item['name_en'] . '; Status: ' . $ge->getMessage() . ";\n";
                        if ($ge->getMessage() == 'OVER_QUERY_LIMIT') {
                            $delay += 100000;
                            echo 'Increasing the delay to ' . $delay . " microseconds\n";
                            $geocode_pending = true;
                        }
                    } catch (\Ministra\Lib\GeoNamesException $gn) {
                        echo 'Bad status for country: ' . $item['country'] . ', city: ' . $item['name_en'] . '; Status: ' . $gn->getMessage() . ";\n";
                        if ($gn->getCode() >= 18 && $gn->getCode() <= 20) {
                            if (self::$geonames_username != 'demo') {
                                self::$geonames_username = 'demo';
                                $geocode_pending = true;
                            } else {
                                throw new \ErrorException('GeoNames credits exceeded');
                            }
                        }
                    } catch (\Exception $e) {
                        echo $e;
                    }
                }
                if (!$geocode_pending) {
                    if (empty($db_city['id'])) {
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('cities', $item);
                    } else {
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('cities', $item, ['id' => $db_city['id']]);
                    }
                }
                \usleep($delay);
            }
        }
    }
    private static function getTimezoneForCity($country, $city)
    {
        $search = \urlencode($country . ' ' . $city);
        $url = 'http://maps.google.com/maps/api/geocode/json?address=' . $search . '&sensor=false&key=' . self::$api_key;
        $result = \file_get_contents($url);
        if (!$result) {
            throw new \ErrorException("Couldn't load geocode");
        }
        $result = \json_decode($result, true);
        \var_dump($url);
        if ($result['status'] != 'OK') {
            throw new \Ministra\Lib\GeoCodeException($result['status']);
        }
        $lat = $result['results'][0]['geometry']['location']['lat'];
        $lng = $result['results'][0]['geometry']['location']['lng'];
        if (empty($lat) || empty($lng)) {
            throw new \ErrorException("Couldn't get location for " . $country . ', ' . $city);
        }
        $timezone_api_url = 'http://api.geonames.org/timezoneJSON?formatted=true&lat=' . $lat . '&lng=' . $lng . '&username=' . self::$geonames_username . '&style=full';
        $timezone_api = \file_get_contents($timezone_api_url);
        if (!$timezone_api) {
            throw new \ErrorException("Couldn't load timezone api");
        }
        $timezone_api = \json_decode($timezone_api, true);
        if (!empty($timezone_api['status'])) {
            throw new \Ministra\Lib\GeoNamesException($timezone_api['status']['message'], $timezone_api['status']['value']);
        }
        if (empty($timezone_api['timezoneId'])) {
            throw new \ErrorException('timezoneId empty, url: ' . $timezone_api_url);
        }
        $timezone_id = $timezone_api['timezoneId'];
        echo 'Country: ' . $timezone_id . '; City: ' . $city . '; Timezone: ' . $timezone_id . ";\n";
        return $timezone_id;
    }
}

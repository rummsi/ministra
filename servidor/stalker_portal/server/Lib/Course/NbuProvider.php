<?php

namespace Ministra\Lib\Course;

class NbuProvider extends \Ministra\Lib\Course\BaseCourseProvider
{
    const API_URL = 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json';
    public function __construct($codes, $db)
    {
        parent::__construct($codes, $db);
        $this->provider = 'nbu';
        $this->title = \_('NBU exchange rate on');
    }
    protected function parseData()
    {
        $content = \file_get_contents(self::API_URL);
        if (!$content) {
            return [];
        }
        $apiData = \json_decode($content, true);
        return $this->extractCurrencies($apiData, $this->codes);
    }
    private function extractCurrencies(array $data, array $codes)
    {
        $rez = [];
        foreach ($data as $item) {
            if (\in_array($item['r030'], $codes)) {
                $date = \DateTime::createFromFormat('d.m.Y', $item['exchangedate']);
                $rez[] = ['code' => $item['r030'], 'currency' => $item['cc'], 'value' => $item['rate'], 'exchange_date' => $date->format('Y-m-d')];
            }
        }
        return $rez;
    }
}

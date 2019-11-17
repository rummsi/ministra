<?php

namespace Ministra\Lib\Course;

class MinfinProvider extends \Ministra\Lib\Course\BaseCourseProvider
{
    const API_URL_TPL = 'http://api.minfin.com.ua/nbu/%s/';
    const USER_AGENT = 'Exchange Rate Widget/1.0 (InfoMir)';
    private $key;
    public function __construct($codes, $db, $options)
    {
        parent::__construct($codes, $db);
        $this->provider = 'minfin';
        $this->key = \array_shift($options);
        $this->title = \_('NBU exchange rate on');
    }
    protected function parseData()
    {
        $codeLabels = \Ministra\Lib\Course\Currencies::codes();
        $codes = $this->prepareCodes();
        $data = $this->getApiData();
        $rez = [];
        foreach ($data as $item) {
            if (\in_array(\strtoupper($item['currency']), $codes)) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $item['date']);
                $rez[] = ['code' => $codeLabels[\strtoupper($item['currency'])], 'currency' => \strtoupper($item['currency']), 'value' => $item['bid'], 'exchange_date' => $date->format('Y-m-d')];
            }
        }
        return $rez;
    }
    private function prepareCodes()
    {
        $currencies = \array_flip(\Ministra\Lib\Course\Currencies::codes());
        $codes = [];
        foreach ($this->codes as $code) {
            if (\array_key_exists($code, $currencies)) {
                $codes[$code] = $currencies[$code];
            }
        }
        return $codes;
    }
    private function getApiData()
    {
        if (!$this->key) {
            throw new \InvalidArgumentException('You must set API key for minfin in config file');
        }
        $url = \sprintf(self::API_URL_TPL, $this->key);
        \ini_set('user_agent', self::USER_AGENT);
        $content = \file_get_contents($url);
        if (!$content) {
            return [];
        }
        return \json_decode($content, true);
    }
}

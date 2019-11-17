<?php

namespace Ministra\Lib\Course;

class BankuaProvider extends \Ministra\Lib\Course\BaseCourseProvider
{
    const API_URL = 'http://bank-ua.com/export/currrate.xml';
    public function __construct($codes, $db)
    {
        parent::__construct($codes, $db);
        $this->provider = 'bankua';
        $this->title = \_('NBU exchange rate on');
    }
    protected function parseData()
    {
        $content = \file_get_contents(self::API_URL);
        if (!$content) {
            return [];
        }
        $xml = \simplexml_load_string($content);
        $rez = [];
        foreach ($xml as $item) {
            if (\in_array((string) $item->code, $this->codes)) {
                $dt = \DateTime::createFromFormat('Y-m-d', (string) $item->date);
                $nominal = (int) $item->size;
                $value = (float) $item->rate;
                if ($value >= 100) {
                    $nominal /= 100;
                    $value /= 100;
                }
                $rez[] = ['code' => (string) $item->code, 'nominal' => $nominal, 'currency' => (string) $item->char3, 'value' => $value, 'exchange_date' => $dt->format('Y-m-d')];
            }
        }
        return $rez;
    }
}

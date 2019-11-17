<?php

namespace Ministra\Lib\Course;

class CbrProvider extends \Ministra\Lib\Course\BaseCourseProvider
{
    const API_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    public function __construct($codes, $db)
    {
        parent::__construct($codes, $db);
        $this->provider = 'cbr';
        $this->title = \_('CBR exchange rate on');
    }
    protected function parseData()
    {
        $context = \stream_context_create(['http' => ['max_redirects' => 101]]);
        $content = \file_get_contents(self::API_URL, false, $context);
        if (!$content) {
            return [];
        }
        $xml = \simplexml_load_string($content);
        $attr = $xml->attributes();
        $rez = [];
        foreach ($xml as $item) {
            if (\in_array((string) $item->NumCode, $this->codes)) {
                $dt = \DateTime::createFromFormat('d.m.Y', (string) $attr['Date']);
                $rez[] = ['code' => (string) $item->NumCode, 'nominal' => (int) $item->Nominal, 'currency' => (string) $item->CharCode, 'value' => (float) \str_replace(',', '.', $item->Value), 'exchange_date' => $dt->format('Y-m-d')];
            }
        }
        return $rez;
    }
}

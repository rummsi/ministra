<?php

namespace Ministra\Admin\Service\EpgParser;

class EpgParserService
{
    private $epg = array();
    private $today;
    public function __construct($epg, $today)
    {
        $lines = \explode("\n", $epg);
        $this->epg = $this->parseLines($lines);
        $this->today = \DateTime::createFromFormat('d-m-Y', $today);
        $this->today->setTime(0, 0);
        $this->analyse();
    }
    private function parseLines($lines)
    {
        $items = [];
        foreach ($lines as $line) {
            $epg = \Ministra\Admin\Service\EpgParser\EpgItem::createFromString($line);
            if ($epg) {
                $items[] = $epg;
            }
        }
        return $items;
    }
    public function getEpg()
    {
        return $this->epg;
    }
    public function getToday()
    {
        return $this->today;
    }
    private function analyse()
    {
        $last = null;
        foreach ($this->epg as $key => $item) {
            $date = $last ? $last->getDate() : null;
            $item->updateDate($this->today, $date);
            if ($last) {
                $last->setEnd($item->getDate());
            }
            $last = $item;
        }
    }
    public function getFirst()
    {
        \reset($this->epg);
        return \current($this->epg);
    }
    public function getLast()
    {
        \end($this->epg);
        return \current($this->epg);
    }
    public function getTill()
    {
        if (\count($this->epg)) {
            return $this->getLast()->getDate();
        }
        $end = clone $this->today;
        $end->setTime(23, 59);
        return $end;
    }
}

<?php

namespace Ministra\Admin\Service\EpgParser;

class EpgItem
{
    private $time;
    private $program;
    private $date;
    private $end;
    private $duration;
    public function __construct($time, $program)
    {
        $this->time = $time;
        $this->program = $program;
    }
    public static function createFromString($str)
    {
        $matches = null;
        if (\preg_match("/^((\\d+):(\\d+))[\\s\t]*([\\S\\s]+)/", $str, $matches) == 1) {
            return new self($matches[1], \trim($matches[4]));
        }
    }
    public function getProgram()
    {
        return $this->program;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getEnd()
    {
        return $this->end;
    }
    public function getDuration()
    {
        return $this->duration;
    }
    public function updateDate($today, $after = null)
    {
        $dateString = $today->format('Y-m-d ') . $this->time;
        $this->date = \DateTime::createFromFormat('Y-m-d H:i', $dateString);
        if (!$after instanceof \DateTimeInterface) {
            return;
        }
        $diff = $this->date->diff($after);
        if ($diff->invert == 0) {
            $this->date->modify('+24 hours');
        }
    }
    public function setEnd($endDate)
    {
        $this->end = $endDate;
        $diff = $this->date->diff($endDate);
        $this->duration = $diff->h * 60 * 60 + $diff->i * 60 + $diff->s;
    }
    public function calcEnd($endDate = null)
    {
        if ($endDate && $this->isSameDay($this->date, $endDate)) {
            $this->setEnd($endDate);
            return;
        }
        $end = clone $this->date;
        $end->setTime(23, 59, 59);
        $this->setEnd($end);
    }
    private function isSameDay($day1, $day2)
    {
        return $day1->format('Y') == $day2->format('Y') && $day1->format('z') == $day2->format('z');
    }
}

<?php

namespace Ministra\Lib\Course;

class CourseComparator
{
    public static function Diff($item)
    {
        if (\count($item) < 2) {
            return 0;
        }
        return \round($item[0]['value'] - $item[1]['value'], 4);
    }
    public static function Trend($diff)
    {
        if ($diff > 0) {
            return 1;
        }
        if ($diff < 0) {
            return -1;
        }
        return 0;
    }
}

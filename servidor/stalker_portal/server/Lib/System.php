<?php

namespace Ministra\Lib;

class System
{
    public static function base64_encode($input)
    {
        return \strtr(\base64_encode($input), '+/=', '-_,');
    }
    public static function base64_decode($input)
    {
        return \base64_decode(\strtr($input, '-_,', '+/='));
    }
    public static function get_all_words()
    {
        return require PROJECT_PATH . '/lang/stb.php';
    }
    public static function convertTimeLengthToHuman($length)
    {
        $hh = \floor($length / 3600);
        $mm = \floor(($length - $hh * 3600) / 60);
        $ss = $length - $hh * 3600 - $mm * 60;
        $result = '';
        if ($hh > 0) {
            $result .= $hh . \_('h') . ' ';
        }
        if ($mm > 0) {
            $result .= $mm . \_('m') . ' ';
        }
        if ($ss > 0) {
            $result .= $ss . \_('s') . ' ';
        }
        return $result;
    }
    public static function convertDatetimeToHuman($timestamp)
    {
        $this_mm = \date('m');
        $this_dd = \date('d');
        $this_yy = \date('Y');
        if ($timestamp > \mktime(0, 0, 0, $this_mm, $this_dd, $this_yy) && $timestamp < \mktime(24, 0, 0, $this_mm, $this_dd, $this_yy)) {
            $human_date = \_('today') . ', ' . \date('H:i', $timestamp);
        } elseif ($timestamp > \mktime(0, 0, 0, $this_mm, $this_dd - 1, $this_yy) && $timestamp < \mktime(24, 0, 0, $this_mm, $this_dd - 1, $this_yy)) {
            $human_date = \_('yesterday') . ', ' . \date('H:i', $timestamp);
        } else {
            $human_date = \date('d.m.Y H:i', $timestamp);
        }
        return $human_date;
    }
    public static function transliterate($st)
    {
        $st = \trim($st);
        $st = \strtr($st, ['а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'g', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'G', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' => 'E', 'ё' => 'yo', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ь' => '', 'ю' => 'yu', 'я' => 'ya', 'Ё' => 'Yo', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ь' => '', 'Ю' => 'Yu', 'Я' => 'Ya', ' ' => '_', '!' => '', '?' => '', ',' => '', '.' => '', '"' => '', '\'' => '', '\\' => '', '/' => '', ';' => '', ':' => '', '«' => '', '»' => '', '`' => '', '-' => '-', '—' => '-']);
        $st = \preg_replace('/[^a-z0-9_-]/i', '', $st);
        return $st;
    }
}

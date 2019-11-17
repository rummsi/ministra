<?php

namespace Ministra\Lib;

class Debug
{
    private static $instance = null;
    public $php_err_str = '';
    public $php_err_counter = 0;
    public $sql_err_str = '';
    public $sql_err_counter = 0;
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new \Ministra\Lib\Debug();
        }
        return self::$instance;
    }
    public function parsePHPError($num, $err, $file, $line)
    {
        if ($num != E_NOTICE) {
            $this->php_err_str .= ' txt: ' . $err . '; file: ' . $file . '; line: ' . $line . '; ';
            ++$this->php_err_counter;
        }
    }
    public function parseSQLError($err)
    {
        $this->sql_err_str .= $err;
        ++$this->sql_err_counter;
    }
    public function getErrorStr()
    {
        $str = 'php errors: ' . $this->php_err_counter . '; sql errors: ' . $this->sql_err_counter . ';';
        if ($this->php_err_str) {
            $str .= ' php err str: ' . $this->php_err_str . ';';
        }
        if ($this->sql_err_str) {
            $str .= ' sql err str: ' . $this->sql_err_str;
        }
        return $str;
    }
}

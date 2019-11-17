<?php

if (!\class_exists('Subsys_JsHttpRequest_Php')) {
    class Subsys_JsHttpRequest_Php
    {
        public $SCRIPT_ENCODING = 'utf-8';
        public $SCRIPT_DECODE_MODE = '';
        public $UNIQ_HASH;
        public $SCRIPT_ID;
        public $LOADER = \null;
        public $QUOTING = \null;
        public $WAS_ERROR;
        public $RESULT;
        public function __construct($enc)
        {
            $this->LOADER = 'SCRIPT';
            if (\preg_match('/(\\d+)((?:-\\w+)?)$/s', $_SERVER['QUERY_STRING'], $m)) {
                $this->SCRIPT_ID = $m[1];
                if ($m[2] == '-xml') {
                    $this->LOADER = 'XMLHttpRequest';
                }
            } else {
                $this->SCRIPT_ID = 0;
            }
            $this->UNIQ_HASH = \md5(\microtime() . \getmypid());
            \ini_set('error_prepend_string', \ini_get('error_prepend_string') . $this->UNIQ_HASH);
            \ini_set('error_append_string', \ini_get('error_append_string') . $this->UNIQ_HASH);
            \ob_start([&$this, '_obHandler']);
            $this->setEncoding($enc);
            $file = $line = \null;
            if (\headers_sent($file, $line)) {
                \trigger_error('HTTP headers are already sent' . ($line !== \null ? " in {$file} on line {$line}" : '') . '. ' . 'Possibly you have extra spaces (or newlines) before first line of the script or any library. ' . 'Please note that Subsys_JsHttpRequest uses its own Content-Type header and fails if ' . 'this header cannot be set. See header() function documentation for details', \E_USER_ERROR);
                exit;
            }
        }
        public function getJsCode()
        {
            return \file_get_contents(\dirname(__FILE__) . '/Js.js');
        }
        public function setEncoding($enc)
        {
            \preg_match('/^(\\S*)(?:\\s+(\\S*))$/', $enc, $p);
            $this->SCRIPT_ENCODING = \strtolower(@$p[1] ? $p[1] : $enc);
            $this->SCRIPT_DECODE_MODE = @$p[2] ? $p[2] : '';
            $this->_correctQueryString();
        }
        public function quoteInput($s)
        {
            if ($this->SCRIPT_DECODE_MODE == 'entities') {
                return \str_replace(['"', '<', '>'], ['&quot;', '&lt;', '&gt;'], $s);
            }
            return \htmlspecialchars($s);
        }
        public function php2js($a)
        {
            if (\is_null($a)) {
                return 'null';
            }
            if ($a === \false) {
                return 'false';
            }
            if ($a === \true) {
                return 'true';
            }
            if (\is_scalar($a)) {
                $a = \addslashes($a);
                $a = \str_replace("\n", '\\n', $a);
                $a = \str_replace("\r", '\\r', $a);
                $a = \preg_replace('{(</)(script)}i', "\$1'+'\$2", $a);
                return "'{$a}'";
            }
            $isList = \true;
            for ($i = 0, \reset($a); $i < \count($a); $i++, \next($a)) {
                if (\key($a) !== $i) {
                    $isList = \false;
                    break;
                }
            }
            $result = [];
            if ($isList) {
                foreach ($a as $v) {
                    $result[] = \Subsys_JsHttpRequest_Php::php2js($v);
                }
                return '[ ' . \implode(',', $result) . ' ]';
            }
            foreach ($a as $k => $v) {
                $result[] = \Subsys_JsHttpRequest_Php::php2js($k) . ': ' . \Subsys_JsHttpRequest_Php::php2js($v);
            }
            return '{ ' . \implode(',', $result) . ' }';
        }
        public function _correctQueryString()
        {
            foreach (['_GET' => $_SERVER['QUERY_STRING'], '_POST' => @$GLOBALS['HTTP_RAW_POST_DATA']] as $dst => $src) {
                if (isset($GLOBALS[$dst])) {
                    $s = \preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
                    $data = \null;
                    \parse_str($s, $data);
                    $GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
                }
            }
            $_REQUEST = (isset($_COOKIE) ? $_COOKIE : []) + (isset($_POST) ? $_POST : []) + (isset($_GET) ? $_GET : []);
            if (\ini_get('register_globals')) {
            }
        }
        public function _obHandler($text)
        {
            if (\preg_match('{' . $this->UNIQ_HASH . '(.*?)' . $this->UNIQ_HASH . '}sx', $text)) {
                $text = \str_replace($this->UNIQ_HASH, '', $text);
                $this->WAS_ERROR = 1;
            }
            \header('Content-type: ' . ($this->LOADER == 'SCRIPT' ? 'text/javascript' : 'text/plain') . '; charset=' . $this->SCRIPT_ENCODING);
            if (!isset($this->RESULT)) {
                $this->RESULT = @$GLOBALS['_RESULT'];
            }
            $result = $this->php2js($this->RESULT);
            $text = "// BEGIN Subsys_JsHttpRequest_Js\n" . "Subsys_JsHttpRequest_Js.dataReady(\n" . '  ' . $this->php2js($this->SCRIPT_ID) . ", // this ID is passed from JavaScript frontend\n" . '  ' . $this->php2js(\trim($text)) . ",\n" . '  ' . $result . "\n" . ")\n" . "// END Subsys_JsHttpRequest_Js\n" . '';
            return $text;
        }
        public function _ucs2EntitiesDecode($data)
        {
            if (\is_array($data)) {
                $d = [];
                foreach ($data as $k => $v) {
                    $d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
                }
                return $d;
            }
            if (\strpos($data, '%u') !== \false) {
                $data = \preg_replace_callback('/%u([0-9A-F]{1,4})/si', [&$this, '_ucs2EntitiesDecodeCallback'], $data);
            }
            return $data;
        }
        public function _ucs2EntitiesDecodeCallback($p)
        {
            $hex = $p[1];
            $dec = \hexdec($hex);
            if ($dec === '38' && $this->SCRIPT_DECODE_MODE == 'entities') {
                $c = '&amp;';
            } else {
                if (\is_callable('iconv')) {
                    $c = @\iconv('UCS-2BE', $this->SCRIPT_ENCODING, \pack('n', $dec));
                } else {
                    $c = $this->_decUcs2Decode($dec, $this->SCRIPT_ENCODING);
                }
                if (!\strlen($c)) {
                    if ($this->SCRIPT_DECODE_MODE == 'entities') {
                        $c = '&#' . $dec . ';';
                    } else {
                        $c = '?';
                    }
                }
            }
            return $c;
        }
        public function _decUcs2Decode($code, $toEnc)
        {
            if ($code < 128) {
                return \chr($code);
            }
            if (isset($this->_encTables[$toEnc])) {
                $p = \array_search($code, $this->_encTables[$toEnc]);
                if ($p !== \false) {
                    return \chr(128 + $p);
                }
            }
            return '';
        }
        public $_encTables = array('windows-1251' => array(0x402, 0x403, 0x201a, 0x453, 0x201e, 0x2026, 0x2020, 0x2021, 0x20ac, 0x2030, 0x409, 0x2039, 0x40a, 0x40c, 0x40b, 0x40f, 0x452, 0x2018, 0x2019, 0x201c, 0x201d, 0x2022, 0x2013, 0x2014, 0x98, 0x2122, 0x459, 0x203a, 0x45a, 0x45c, 0x45b, 0x45f, 0xa0, 0x40e, 0x45e, 0x408, 0xa4, 0x490, 0xa6, 0xa7, 0x401, 0xa9, 0x404, 0xab, 0xac, 0xad, 0xae, 0x407, 0xb0, 0xb1, 0x406, 0x456, 0x491, 0xb5, 0xb6, 0xb7, 0x451, 0x2116, 0x454, 0xbb, 0x458, 0x405, 0x455, 0x457, 0x410, 0x411, 0x412, 0x413, 0x414, 0x415, 0x416, 0x417, 0x418, 0x419, 0x41a, 0x41b, 0x41c, 0x41d, 0x41e, 0x41f, 0x420, 0x421, 0x422, 0x423, 0x424, 0x425, 0x426, 0x427, 0x428, 0x429, 0x42a, 0x42b, 0x42c, 0x42d, 0x42e, 0x42f, 0x430, 0x431, 0x432, 0x433, 0x434, 0x435, 0x436, 0x437, 0x438, 0x439, 0x43a, 0x43b, 0x43c, 0x43d, 0x43e, 0x43f, 0x440, 0x441, 0x442, 0x443, 0x444, 0x445, 0x446, 0x447, 0x448, 0x449, 0x44a, 0x44b, 0x44c, 0x44d, 0x44e, 0x44f), 'koi8-r' => array(0x2500, 0x2502, 0x250c, 0x2510, 0x2514, 0x2518, 0x251c, 0x2524, 0x252c, 0x2534, 0x253c, 0x2580, 0x2584, 0x2588, 0x258c, 0x2590, 0x2591, 0x2592, 0x2593, 0x2320, 0x25a0, 0x2219, 0x221a, 0x2248, 0x2264, 0x2265, 0xa0, 0x2321, 0xb0, 0xb2, 0xb7, 0xf7, 0x2550, 0x2551, 0x2552, 0x451, 0x2553, 0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255a, 0x255b, 0x255c, 0x255d, 0x255e, 0x255f, 0x2560, 0x2561, 0x401, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567, 0x2568, 0x2569, 0x256a, 0x256b, 0x256c, 0xa9, 0x44e, 0x430, 0x431, 0x446, 0x434, 0x435, 0x444, 0x433, 0x445, 0x438, 0x439, 0x43a, 0x43b, 0x43c, 0x43d, 0x43e, 0x43f, 0x44f, 0x440, 0x441, 0x442, 0x443, 0x436, 0x432, 0x44c, 0x44b, 0x437, 0x448, 0x44d, 0x449, 0x447, 0x44a, 0x42e, 0x410, 0x411, 0x426, 0x414, 0x415, 0x424, 0x413, 0x425, 0x418, 0x419, 0x41a, 0x41b, 0x41c, 0x41d, 0x41e, 0x41f, 0x42f, 0x420, 0x421, 0x422, 0x423, 0x416, 0x412, 0x42c, 0x42b, 0x417, 0x428, 0x42d, 0x429, 0x427, 0x42a));
    }
}

<?php

namespace Ministra\Lib;

class JsHttpRequest
{
    public $SCRIPT_ENCODING = 'utf-8';
    public $SCRIPT_DECODE_MODE = '';
    public $LOADER = null;
    public $ID = null;
    public $RESULT = null;
    public $_uniqHash;
    public $_magic = 14623;
    public $_prevDisplayErrors = null;
    public $_contentTypes = array('script' => 'text/javascript', 'xml' => 'text/plain', 'form' => 'text/html', '' => 'text/plain');
    public $_toUtfFailed = false;
    public $_nonAsciiChars = '';
    public $_unicodeConvMethod = null;
    public $_emergBuffer = null;
    public $_encTables = array('windows-1251' => array(0x402, 0x403, 0x201a, 0x453, 0x201e, 0x2026, 0x2020, 0x2021, 0x20ac, 0x2030, 0x409, 0x2039, 0x40a, 0x40c, 0x40b, 0x40f, 0x452, 0x2018, 0x2019, 0x201c, 0x201d, 0x2022, 0x2013, 0x2014, 0x98, 0x2122, 0x459, 0x203a, 0x45a, 0x45c, 0x45b, 0x45f, 0xa0, 0x40e, 0x45e, 0x408, 0xa4, 0x490, 0xa6, 0xa7, 0x401, 0xa9, 0x404, 0xab, 0xac, 0xad, 0xae, 0x407, 0xb0, 0xb1, 0x406, 0x456, 0x491, 0xb5, 0xb6, 0xb7, 0x451, 0x2116, 0x454, 0xbb, 0x458, 0x405, 0x455, 0x457, 0x410, 0x411, 0x412, 0x413, 0x414, 0x415, 0x416, 0x417, 0x418, 0x419, 0x41a, 0x41b, 0x41c, 0x41d, 0x41e, 0x41f, 0x420, 0x421, 0x422, 0x423, 0x424, 0x425, 0x426, 0x427, 0x428, 0x429, 0x42a, 0x42b, 0x42c, 0x42d, 0x42e, 0x42f, 0x430, 0x431, 0x432, 0x433, 0x434, 0x435, 0x436, 0x437, 0x438, 0x439, 0x43a, 0x43b, 0x43c, 0x43d, 0x43e, 0x43f, 0x440, 0x441, 0x442, 0x443, 0x444, 0x445, 0x446, 0x447, 0x448, 0x449, 0x44a, 0x44b, 0x44c, 0x44d, 0x44e, 0x44f), 'koi8-r' => array(0x2500, 0x2502, 0x250c, 0x2510, 0x2514, 0x2518, 0x251c, 0x2524, 0x252c, 0x2534, 0x253c, 0x2580, 0x2584, 0x2588, 0x258c, 0x2590, 0x2591, 0x2592, 0x2593, 0x2320, 0x25a0, 0x2219, 0x221a, 0x2248, 0x2264, 0x2265, 0xa0, 0x2321, 0xb0, 0xb2, 0xb7, 0xf7, 0x2550, 0x2551, 0x2552, 0x451, 0x2553, 0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255a, 0x255b, 0x255c, 0x255d, 0x255e, 0x255f, 0x2560, 0x2561, 0x401, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567, 0x2568, 0x2569, 0x256a, 0x256b, 0x256c, 0xa9, 0x44e, 0x430, 0x431, 0x446, 0x434, 0x435, 0x444, 0x433, 0x445, 0x438, 0x439, 0x43a, 0x43b, 0x43c, 0x43d, 0x43e, 0x43f, 0x44f, 0x440, 0x441, 0x442, 0x443, 0x436, 0x432, 0x44c, 0x44b, 0x437, 0x448, 0x44d, 0x449, 0x447, 0x44a, 0x42e, 0x410, 0x411, 0x426, 0x414, 0x415, 0x424, 0x413, 0x425, 0x418, 0x419, 0x41a, 0x41b, 0x41c, 0x41d, 0x41e, 0x41f, 0x42f, 0x420, 0x421, 0x422, 0x423, 0x416, 0x412, 0x42c, 0x42b, 0x417, 0x428, 0x42d, 0x429, 0x427, 0x42a));
    public function JsHttpRequest($enc)
    {
        global $JsHttpRequest_Active;
        $GLOBALS['_RESULT'] =& $this->RESULT;
        if (\preg_match('/^(.*)(?:&|^)JsHttpRequest=(?:(\\d+)-)?([^&]+)((?:&|$).*)$/s', @$_SERVER['QUERY_STRING'], $m)) {
            $this->ID = $m[2];
            $this->LOADER = \strtolower($m[3]);
            $_SERVER['QUERY_STRING'] = \preg_replace('/^&+|&+$/s', '', \preg_replace('/(^|&)' . \session_name() . '=[^&]*&?/s', '&', $m[1] . $m[4]));
            unset($_GET['JsHttpRequest'], $_REQUEST['JsHttpRequest'], $_GET[\session_name()], $_POST[\session_name()], $_REQUEST[\session_name()]);
            $this->_unicodeConvMethod = \function_exists('mb_convert_encoding') ? 'mb' : (\function_exists('iconv') ? 'iconv' : null);
            $this->_emergBuffer = \str_repeat('a', 1024 * 200);
            $this->_uniqHash = \md5('JsHttpRequest' . \microtime() . \getmypid());
            $this->_prevDisplayErrors = \ini_get('display_errors');
            \ini_set('display_errors', $this->_magic);
            \ini_set('error_prepend_string', $this->_uniqHash . \ini_get('error_prepend_string'));
            \ini_set('error_append_string', \ini_get('error_append_string') . $this->_uniqHash);
            if (\function_exists('xdebug_disable')) {
                \call_user_func_array('xdebug_disable', []);
            }
            \ob_start([&$this, '_obHandler']);
            $JsHttpRequest_Active = true;
            $this->setEncoding($enc);
            $file = $line = null;
            $headersSent = \version_compare(PHP_VERSION, '4.3.0') < 0 ? \headers_sent() : \headers_sent($file, $line);
            if ($headersSent) {
                \trigger_error('HTTP headers are already sent' . ($line !== null ? " in {$file} on line {$line}" : ' somewhere in the script') . '. ' . 'Possibly you have an extra space (or a newline) before the first line of the script or any' . ' library. ' . 'Please note that JsHttpRequest uses its own Content-Type header and fails if ' . 'this header cannot be set. See header() function documentation for more details', E_USER_ERROR);
                exit;
            }
        } else {
            $this->ID = 0;
            $this->LOADER = 'unknown';
            $JsHttpRequest_Active = false;
        }
    }
    public function setEncoding($enc)
    {
        \preg_match('/^(\\S*)(?:\\s+(\\S*))$/', $enc, $p);
        $this->SCRIPT_ENCODING = \strtolower(!empty($p[1]) ? $p[1] : $enc);
        $this->SCRIPT_DECODE_MODE = !empty($p[2]) ? $p[2] : '';
    }
    public function isActive()
    {
        return !empty($GLOBALS['JsHttpRequest_Active']);
    }
    public function getJsCode()
    {
        return \file_get_contents(\dirname(__FILE__) . '/JsHttpRequest.js');
    }
    public function quoteInput($s)
    {
        if ($this->SCRIPT_DECODE_MODE == 'entities') {
            return \str_replace(['"', '<', '>'], ['&quot;', '&lt;', '&gt;'], $s);
        }
        return \htmlspecialchars($s);
    }
    public function _correctSuperglobals()
    {
        if ($this->LOADER == 'form') {
            return;
        }
        $rawPost = \strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0 ? isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : @\file_get_contents('php://input') : null;
        $source = ['_GET' => !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null, '_POST' => $rawPost];
        foreach ($source as $dst => $src) {
            $s = \preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
            $data = null;
            \parse_str($s, $data);
            $GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
        }
        $GLOBALS['HTTP_GET_VARS'] = $_GET;
        $GLOBALS['HTTP_POST_VARS'] = $_POST;
        $_REQUEST = (isset($_COOKIE) ? $_COOKIE : []) + (isset($_POST) ? $_POST : []) + (isset($_GET) ? $_GET : []);
        if (\ini_get('register_globals')) {
        }
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
        if (\strpos($data, '%u') !== false) {
            $data = \preg_replace_callback('/%u([0-9A-F]{1,4})/si', [&$this, '_ucs2EntitiesDecodeCallback'], $data);
        }
        return $data;
    }
    public function _obHandler($text)
    {
        unset($this->_emergBuffer, $GLOBALS['JsHttpRequest_Active']);
        $wasFatalError = false;
        if (\preg_match_all("/{$this->_uniqHash}(.*?){$this->_uniqHash}/sx", $text, $m)) {
            $needRemoveErrorMessages = !\ini_get('display_errors') || !$this->_prevDisplayErrors && \ini_get('display_errors') == $this->_magic;
            foreach ($m[0] as $error) {
                if (\preg_match('/\\bFatal error(<.*?>)?:/i', $error)) {
                    $wasFatalError = true;
                }
                if ($needRemoveErrorMessages) {
                    $text = \str_replace($error, '', $text);
                } else {
                    $text = \str_replace($this->_uniqHash, '', $text);
                }
            }
        }
        if ($wasFatalError) {
            $this->RESULT = null;
        } else {
            if (!isset($this->RESULT)) {
                global $_RESULT;
                $this->RESULT = $_RESULT;
            }
            if ($this->RESULT === null) {
                $this->RESULT = false;
            }
        }
        $status = $this->RESULT === null ? 500 : 200;
        $result = ['id' => $this->ID, 'js' => $this->RESULT, 'text' => $text];
        $encoding = $this->SCRIPT_ENCODING;
        $text = null;
        if (\function_exists('array_walk_recursive') && \function_exists('json_encode') && $this->_unicodeConvMethod) {
            $this->_nonAsciiChars = \implode('', \array_map('chr', \range(128, 255)));
            $this->_toUtfFailed = false;
            $resultUtf8 = $result;
            \array_walk_recursive($resultUtf8, [&$this, '_toUtf8_callback'], $this->SCRIPT_ENCODING);
            if (!$this->_toUtfFailed) {
                $text = \json_encode($resultUtf8);
                $encoding = 'UTF-8';
            }
        }
        if ($text === null) {
            $text = $this->php2js($result);
        }
        if ($this->LOADER != 'xml') {
            $text = '' . ($this->LOADER == 'form' ? 'top && top.JsHttpRequestGlobal && top.JsHttpRequestGlobal' : 'JsHttpRequest') . '.dataReady(' . $text . ")\n" . '';
            if ($this->LOADER == 'form') {
                $text = '<script type="text/javascript" language="JavaScript"><!--' . "\n{$text}" . '//--></script>';
            }
            $status = 200;
        }
        if ($this->RESULT === null) {
            if (PHP_SAPI == 'cgi') {
                \header("Status: {$status}");
            } else {
                \header("HTTP/1.1 {$status}");
            }
        }
        $ctype = !empty($this->_contentTypes[$this->LOADER]) ? $this->_contentTypes[$this->LOADER] : $this->_contentTypes[''];
        \header("Content-type: {$ctype}; charset={$encoding}");
        return $text;
    }
    public function php2js($a = false)
    {
        if (\is_null($a)) {
            return 'null';
        }
        if ($a === false) {
            return 'false';
        }
        if ($a === true) {
            return 'true';
        }
        if (\is_int($a)) {
            return (string) $a;
        }
        if (\is_scalar($a)) {
            if (\is_float($a)) {
                $a = \str_replace(',', '.', (string) $a);
            }
            static $jsonReplaces = array(array('\\', '/', "\n", "\t", "\r", "\\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\\"'));
            return '"' . \str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
        }
        $isList = true;
        for ($i = 0, \reset($a); $i < \count($a); $i++, \next($a)) {
            if (\key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = [];
        if ($isList) {
            foreach ($a as $v) {
                $result[] = \Ministra\Lib\JsHttpRequest::php2js($v);
            }
            return '[ ' . \implode(', ', $result) . ' ]';
        }
        foreach ($a as $k => $v) {
            $result[] = \Ministra\Lib\JsHttpRequest::php2js($k) . ': ' . \Ministra\Lib\JsHttpRequest::php2js($v);
        }
        return '{ ' . \implode(', ', $result) . ' }';
    }
    public function _toUtf8_callback(&$v, $k, $fromEnc)
    {
        if ($v === null || \is_bool($v) || \is_int($v)) {
            return;
        }
        if ($this->_toUtfFailed || !\is_scalar($v) || \strpbrk($k, $this->_nonAsciiChars) !== false) {
            $this->_toUtfFailed = true;
        } else {
            $v = $this->_unicodeConv($fromEnc, 'UTF-8', $v);
        }
    }
    public function _unicodeConv($fromEnc, $toEnc, $v)
    {
        if ($this->_unicodeConvMethod == 'iconv') {
            return \iconv($fromEnc, $toEnc, $v);
        }
        return \mb_convert_encoding($v, $toEnc, $fromEnc);
    }
    public function _ucs2EntitiesDecodeCallback($p)
    {
        $hex = $p[1];
        $dec = \hexdec($hex);
        if ($dec === '38' && $this->SCRIPT_DECODE_MODE == 'entities') {
            $c = '&amp;';
        } else {
            if ($this->_unicodeConvMethod) {
                $c = @$this->_unicodeConv('UCS-2BE', $this->SCRIPT_ENCODING, \pack('n', $dec));
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
        static $flippedTable = null;
        if ($code < 128) {
            return \chr($code);
        }
        if (isset($this->_encTables[$toEnc])) {
            if (!$flippedTable) {
                $flippedTable = \array_flip($this->_encTables[$toEnc]);
            }
            if (isset($flippedTable[$code])) {
                return \chr(128 + $flippedTable[$code]);
            }
        } elseif ($toEnc == 'utf-8' || $toEnc == 'utf8') {
            if ($code < 0x800) {
                return \chr(0xc0 + ($code >> 6)) . \chr(0x80 + ($code & 0x3f));
            }
            return \chr(0xe0 + ($code >> 12)) . \chr(0x80 + (0x3f & $code >> 6)) . \chr(0x80 + ($code & 0x3f));
        }
        return '';
    }
}

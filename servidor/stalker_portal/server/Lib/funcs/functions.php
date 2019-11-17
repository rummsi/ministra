<?php

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
if (!\function_exists('join_paths')) {
    function join_paths()
    {
        $paths = [];
        foreach (\func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }
        return \preg_replace('#/+#', '/', \implode('/', $paths));
    }
    function is_available_file($base_path, $file_path, $file_name, $file_default_ext = '')
    {
        $return_path = \join_paths($file_path, $file_name);
        if (!\pathinfo($return_path, \PATHINFO_EXTENSION)) {
            $return_path .= '.' . $file_default_ext;
        }
        return \is_readable(\join_paths($base_path, $return_path)) ? $return_path : '';
    }
    function delTree($dir)
    {
        $files = \array_diff(\scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            if (\is_link($path)) {
                \exec("rm {$path}");
            }
            \is_dir($path) ? \delTree($path) : \unlink($path);
        }
        return (bool) \rmdir($dir);
    }
    function get_save_folder($id)
    {
        $dir_name = \ceil($id / 100);
        $dir_path = \realpath(\PROJECT_PATH . '/../' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('screenshots_path', 'screenshots/')) . '/' . $dir_name;
        if (!\is_dir($dir_path)) {
            \umask(0);
            if (!\mkdir($dir_path, 0777)) {
                return -1;
            }
            return $dir_path;
        }
        return $dir_path;
    }
    function transliterate($st)
    {
        $st = \trim($st);
        $st = \strtr($st, ['а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'g', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'G', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' => 'E', 'ё' => 'yo', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ь' => '', 'ю' => 'yu', 'я' => 'ya', 'Ё' => 'Yo', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ь' => '', 'Ю' => 'Yu', 'Я' => 'Ya', ' ' => '_', '!' => '', '?' => '', ',' => '', '.' => '', '"' => '', '\'' => '', '\\' => '', '/' => '', ';' => '', ':' => '', '«' => '', '»' => '', '`' => '', '-' => '-', '—' => '-']);
        $st = \preg_replace('/[^a-z0-9_-]/i', '', $st);
        return $st;
    }
    function check_db_user_login($login, $pass)
    {
        $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['login' => $login])->get()->first();
        if ($user['pass'] == \md5($pass)) {
            $_SESSION['uid'] = $user['id'];
            $_SESSION['login'] = $login;
            $_SESSION['pass'] = $user['pass'];
            $_SESSION['access'] = $user['access'];
            return 1;
        }
        return 0;
    }
    function check_session_user_login()
    {
        if (empty($_SESSION['login']) || empty($_SESSION['pass'])) {
            return 0;
        }
        $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['login' => $_SESSION['login']])->get()->first();
        if ($user['pass'] == $_SESSION['pass']) {
            return 1;
        }
        return 0;
    }
    function moderator_access()
    {
        if (!\check_session_user_login()) {
            \header('Location: login.php');
            exit;
        }
    }
    function check_access($num = array())
    {
        $num[] = 0;
        if (\in_array($_SESSION['access'], $num)) {
            return 1;
        }
        return 0;
    }
    function get_cur_playing_type($in_param = '')
    {
        return \get_cur_active_playing_type($in_param);
    }
    function get_cur_active_playing_type($in_param = '')
    {
        $now_timestamp = \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
        if ($in_param == '') {
            $in_param = $_GET['in_param'];
        }
        if ($in_param == 'itv') {
            $type = 1;
        } else {
            if ($in_param == 'vclub') {
                $type = 2;
            } else {
                if ($in_param == 'karaoke') {
                    $type = 3;
                } else {
                    if ($in_param == 'aclub') {
                        $type = 4;
                    } else {
                        if ($in_param == 'radio') {
                            $type = 5;
                        } else {
                            $type = 100;
                        }
                    }
                }
            }
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['now_playing_type' => $type, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, $now_timestamp)])->get()->counter();
    }
    function get_cur_infoportal()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['now_playing_type>=' => 20, 'now_playing_type<=' => 29, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2)])->get()->counter();
    }
    function get_last5min_play($in_param = '')
    {
        if ($in_param == '') {
            $in_param = $_GET['in_param'];
        }
        $in_param_arr = ['itv' => 1, 'vclub' => 2, 'karaoke' => 3, 'aclub' => 4, 'radio' => 5];
        $now_timestamp = \time() - 330;
        $now_time = \date('Y-m-d H:i:s', $now_timestamp);
        if (!\array_key_exists($in_param, $in_param_arr)) {
            return 0;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_log')->count()->where(['time>' => $now_time, 'type' => $in_param_arr[$in_param], 'action' => 'play'])->groupby('mac')->get()->counter();
    }
    function get_cur_users($in_param = '')
    {
        if ($in_param == '') {
            $in_param = $_GET['in_param'];
        }
        $now_timestamp = \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
        $now_time = \date('Y-m-d H:i:s', $now_timestamp);
        if ($in_param == 'online') {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['keep_alive>' => $now_time])->get()->counter();
        } elseif ($in_param == 'offline') {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['keep_alive<' => $now_time])->get()->counter();
        }
        return 0;
    }
    function datetime2timestamp($datetime)
    {
        \preg_match("/(\\d+)-(\\d+)-(\\d+) (\\d+):(\\d+):(\\d+)/", $datetime, $arr);
        return @\mktime($arr[4], $arr[5], $arr[6], $arr[2], $arr[3], $arr[1]);
    }
    function get_str_lang($str)
    {
        $lang = 0;
        $first_l = \substr($str, 0, 1);
        if (\preg_match('/[а-я,А-Я]/', $first_l)) {
            $lang = 0;
        } else {
            if (\preg_match('/[a-z,A-Z]/', $first_l)) {
                $lang = 1;
            } else {
                if (\preg_match('/[0-9]/', $first_l)) {
                    $lang = 2;
                }
            }
        }
        return $lang;
    }
    function js_redirect($to, $msg = '', $delay = 2)
    {
        echo <<<REDIRECT
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="refresh" content="{$delay}; URL={$to}">
    </head>
    <body>{$msg}</body>
    </html>
REDIRECT;
    }
    function check_keep_alive($time)
    {
        $keep_alive_ts = \datetime2timestamp($time);
        $now_ts = \time();
        $dif_ts = $now_ts - $keep_alive_ts;
        if ($dif_ts > \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2) {
            return 0;
        }
        return 1;
    }
    function check_keep_alive_txt($time)
    {
        if (\check_keep_alive($time)) {
            return '<font color="Green">online</font>';
        }
        return '<font color="Red">offline</font>';
    }
    function get_sub_channels($id = 0)
    {
        if ($id == 0) {
            $id = (int) @$_GET['id'];
        }
        $sub_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $id])->get()->first('sub_ch');
        $sub_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($sub_ch));
        if (!\is_array($sub_ch)) {
            return [];
        }
        return $sub_ch;
    }
    function get_bonus_channels($id = 0)
    {
        if ($id == 0) {
            $id = (int) @$_GET['id'];
        }
        $bonus_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $id])->get()->first('bonus_ch');
        $bonus_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($bonus_ch));
        if (!\is_array($bonus_ch)) {
            return [];
        }
        return $bonus_ch;
    }
    function kop2grn($kops)
    {
        $grn = \floor($kops / 100);
        $kop = $kops - $grn * 100;
        if ($kop < 10) {
            $kop = '0' . $kop;
        }
        return $grn . '.' . $kop;
    }
    function set_karaoke_status($id, $val)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['status' => $val], ['id' => $id]);
    }
    function get_storage_use($in_param = '')
    {
        if ($in_param == '') {
            $in_param = $_GET['in_param'];
        }
        $now_timestamp = \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
        $now_time = \date('Y-m-d H:i:s', $now_timestamp);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['keep_alive>' => $now_time, 'storage_name' => $in_param, 'now_playing_type' => 2])->get()->counter();
    }
    function getHash($hasher, $randomString)
    {
        return \hash($hasher, $randomString);
    }
    function getServerProtocol()
    {
        return 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 's' : '');
    }
    function getServerHost()
    {
        if (\strpos($_SERVER['HTTP_HOST'], ':') > 0) {
            return $_SERVER['HTTP_HOST'];
        }
        return $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
    }
    function stalker_autoloader($class)
    {
        if (\strpos($class, 'Stalker\\') === 0 || \strpos($class, '\\Stalker\\') === 0) {
            $original = \str_replace('Stalker\\', 'Ministra\\', $class);
            $file = __DIR__ . '/../..' . \str_replace('\\', '/', \substr($class, 7, \strlen($class))) . '.php';
            if (\file_exists($file)) {
                require_once $file;
                \class_alias($original, $class);
                return;
            }
            throw new \RuntimeException("Class {$class} does not find");
        }
        if (!\strpos($class, '\\') === \false && (\strpos($class, '\\') !== 0 || \strpos($class, '\\') !== 0 && \substr_count($class, '\\') > 1)) {
            return;
        }
        $libClass = __DIR__ . "/../{$class}.php";
        if (\file_exists($libClass)) {
            require_once $libClass;
            \class_alias("Ministra\\Lib\\{$class}", $class, \true);
            return;
        }
    }
    function error($msg = '')
    {
        return '{"status":"OK","results":false,"error":"' . $msg . '"}';
    }
    function _log($str)
    {
        echo $str . "\n";
    }
    function strxor($string, $key)
    {
        $text = $string;
        $outText = '';
        for ($i = 0; $i < \strlen($text);) {
            for ($j = 0; $j < \strlen($key) && $i < \strlen($text); $j++, $i++) {
                $outText .= $text[$i] ^ $key[$j];
            }
        }
        return $outText;
    }
    \spl_autoload_register('stalker_autoloader');
}

<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
if (empty($_REQUEST['login']) || empty($_REQUEST['password'])) {
    echo '{"status":"ERROR","results":false,"error":"Login and password required"}';
    exit;
}
$login = $_REQUEST['login'];
$password = $_REQUEST['password'];
$mac = isset($_REQUEST['mac']) ? $_REQUEST['mac'] : '';
$possible_user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['login' => $login])->get()->first();
if (\strlen($possible_user['password']) == 32 && \md5(\md5($password) . $possible_user['id']) == $possible_user['password'] || \strlen($possible_user['password']) < 32 && $password == $possible_user['password']) {
    if ($possible_user['mac'] == '' || \strtolower($possible_user['mac']) == \strtolower($mac)) {
        $user = $possible_user;
    }
}
if (empty($user)) {
    echo \error('User not exist or login-password mismatch');
} else {
    echo '{"status":"OK","results":true}';
}

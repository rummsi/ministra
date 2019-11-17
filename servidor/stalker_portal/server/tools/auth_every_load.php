<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\k5f3a0eff0bb2b863dd6257af9a557248\fcdf8a135de2e2533b358a0a4bb5616a;
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8::a0a3921a25e19d949bd4be9d65f0e1e0()->R35cd2e80d7a2fc41598228f4269aed88('auth_url', '')) {
    echo '{"status":"ERROR","results":false,"error":"Auth disabled"}';
    exit;
}
if (empty($_REQUEST['login']) || empty($_REQUEST['password'])) {
    echo '{"status":"ERROR","results":false,"error":"Login and password required"}';
    exit;
}
\sleep(1);
$login = $_REQUEST['login'];
$password = $_REQUEST['password'];
$mac = isset($_REQUEST['mac']) ? $_REQUEST['mac'] : '';
$profileObject = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8::a0a3921a25e19d949bd4be9d65f0e1e0()->e1e51374dfecca5523625e79fa3dd3bf()->get(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\k5f3a0eff0bb2b863dd6257af9a557248\fcdf8a135de2e2533b358a0a4bb5616a::class);
$possibleUser = $profileObject->ea123d0fbe9e17918af93ddd56245a09($login);
if (\md5(\md5($password) . $possibleUser['id']) === $possibleUser['password']) {
    $user = $possibleUser;
}
if (empty($user)) {
    echo \error('User not exist or login-password mismatch');
} else {
    if ($mac) {
        $profileObject->P5a68e6f0a14800a89ab76cd888ca40c9(['mac' => '', 'device_id' => '', 'device_id2' => '', 'access_token' => ''], ['mac' => $mac]);
    }
    $profileObject->P5a68e6f0a14800a89ab76cd888ca40c9(['mac' => $mac], ['id' => $user['id']]);
    echo '{"status":"OK","results":true}';
}

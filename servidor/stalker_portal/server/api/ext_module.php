<?php

require __DIR__ . '/common.php';
use Ministra\Lib\AppsManager;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3::d5de025803f2de6a57d75fa98ac892b8c('STB Api: ' . __FILE__);
if (empty($_GET['name']) || empty($_GET['mac'])) {
    exit;
}
$alias = \str_replace('external_', '', $_GET['name']);
$app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('apps')->where(['alias' => $alias])->get()->first();
if (empty($app) || $app['status'] == 0) {
    exit;
}
$apps = new \Ministra\Lib\AppsManager();
$app = $apps->getAppInfoWoFetch($app['id']);
if (!$app['installed']) {
    exit;
}
\header('Content-Type: application/x-javascript');
$user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['mac' => $_GET['mac']])->get()->first();
$disabled_for_mag200_apps = ['youtube.com', 'zoomby', 'megogo', 'olltv'];
if ($user && $user['stb_type'] == 'MAG200' && \in_array(\strtolower($app['name']), $disabled_for_mag200_apps) !== \false) {
    exit;
}
$user_theme = empty($user['theme']) || !\array_key_exists($user['theme'], \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2()) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template') : $user['theme'];
$icon = $app['app_url'] . '/img/{0}/' . $app['icons'] . '/' . ($user_theme == 'default' ? '2010' : '2014') . '.png';
if ($app['options'] && ($options = \json_decode($app['options'], \true))) {
    $app['app_url'] .= (\strpos($app['app_url'], '?') ? '&' : '?') . \http_build_query($options);
}
?>
/**
* Redirection to <?php 
echo $app['name'];
?> module.
*/
(function(){

main_menu.add('<?php 
echo $app['name'];
?>', [], '<?php 
echo $icon;
?>', function(){

var params = '';

var url = '<?php 
echo $app['app_url'];
?>';

if (stb.user['web_proxy_host']){
params += (url.indexOf('?') == -1 ? '?' : '&')+'proxy=http://';
if (stb.user['web_proxy_user']){
params += stb.user['web_proxy_user']+':'+stb.user['web_proxy_pass']+'@';
}
params += stb.user['web_proxy_host']+':' +stb.user['web_proxy_port'];
}

stb.setFrontPanel('.');

if (!params && url.indexOf('?') == -1){
params += '?';
}else{
params += '&';
}

params = stb.add_referrer(params, this.module.layer_name);

_debug('url', url+params);

window.location = url+params;

}, {layer_name : "external_<?php 
echo $app['alias'];
?>"});

loader.next();
})();


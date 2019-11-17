<?php

if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
$app['twig.options.cache'] = __DIR__ . '/resources/cache/twig';
$theme = [];
$base_twig_path = __DIR__ . '/../resources/views';
foreach (\array_diff(\scandir($base_twig_path), ['..', '.']) as $theme_dir) {
    $theme_dir_path = $base_twig_path . '/' . $theme_dir;
    if (\is_dir($theme_dir_path)) {
        $theme[$theme_dir] = $theme_dir_path . '/';
    }
}
$app['util.path'] = \realpath(__DIR__ . '/../..') . '/deploy/clear_key_util';
$app['themes'] = $theme;
$app['db.host'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('mysql_host');
$app['db.port'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('mysql_port');
$app['db.user'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('mysql_user');
$app['db.password'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('mysql_pass');
$app['db.dbname'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('db_name');
$app['memcache.options'] = ['memcache.options' => ['host' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('memcache_host', 'localhost'), 'default_timeout' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('admin_panel_sidebar_cache_time', 1800)]];
$container['security.default_encoder'] = function () {
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('md5', \false, 0);
};

<?php

\chdir(__DIR__);
require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\SmartLauncherAppsManager;
$zone_url = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('max_cdn_pull_zone_url', '');
$write_base_url = !empty($zone_url) ? "document.write(\"<base href='http://{$zone_url}/' />\");" : '';
$language = isset($_GET['language']) ? $_GET['language'] : 'en';
$allowed_languages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales');
$allowed_languages_map = [];
foreach ($allowed_languages as $loc) {
    $allowed_languages_map[\substr($loc, 0, 2)] = $loc;
}
if (isset($allowed_languages_map[$language])) {
    $locale = $allowed_languages_map[$language];
} elseif (\count($allowed_languages_map) > 0) {
    \reset($allowed_languages_map);
    $locale = $allowed_languages_map[\key($allowed_languages_map)];
} else {
    $locale = 'en_GB.utf8';
}
\setlocale(\LC_MESSAGES, $locale);
\putenv('LC_MESSAGES=' . $locale);
$app_manager = new \Ministra\Lib\SmartLauncherAppsManager($language);
$player_app = $app_manager->getAppInfoByUrl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('web_player_app_url', ''));
if (!empty($player_app) && $player_app['installed']) {
    $path = $player_app['app_path'] . \DIRECTORY_SEPARATOR . 'app' . \str_replace('/player', '', $_SERVER['REQUEST_URI']);
    $request = \str_replace('/player/', '', $_SERVER['REQUEST_URI']);
    if (empty($request)) {
        $request = 'index.html';
    }
    $path = \explode('?', $player_app['app_path'] . \DIRECTORY_SEPARATOR . 'app' . \DIRECTORY_SEPARATOR . $request);
    $path = $path[0];
    $ouput = '';
    if (\is_file($path) && \is_readable($path)) {
        if (!\headers_sent()) {
            $mime_type = \mime_content_type($path);
            if ($mime_type !== \false) {
                $ext = \explode('.', $path);
                $ext = \end($ext);
                $replace_mime = ['css' => 'text/css', 'js' => 'text/javascript'];
                if (\array_key_exists($ext, $replace_mime)) {
                    $mime_type = $replace_mime[$ext];
                }
                if (\strpos($mime_type, 'text') !== \false) {
                    $mime_type .= '; charset=utf-8';
                }
            }
            \header("Content-Type: {$mime_type}");
        }
        $ouput = \file_get_contents($path);
    }
    echo $ouput;
}

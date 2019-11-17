<?php

require __DIR__ . '/common.php';
if (empty($_GET['uid'])) {
    exit;
}
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\SmartLauncherAppsManager;
use Ministra\Lib\SmartLauncherAppsManagerException;
use Ministra\Lib\User;
$config = ['options' => [], 'themes' => [], 'apps' => []];
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
$installed_apps = $app_manager->getInstalledApps();
$installed_apps_names = \array_map(function ($app) {
    return 'launcher_' . $app['alias'];
}, $installed_apps);
$user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::z55bad8d1e166d966765492584ab3ab41((int) $_GET['uid']);
if (!empty($user)) {
    \Ministra\Lib\User::getInstance($user['id']);
}
$all_modules = \array_merge(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('all_modules'), $installed_apps_names);
$disabled_modules = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::d7814074e003e3e6aea2e49c0c79a49d((int) $_GET['uid']);
$core = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['type' => 'core', 'status' => 1])->get()->first();
$core_entry = 'app';
if (!empty($core['config'])) {
    $core['config'] = \json_decode($core['config'], \true);
    if (isset($core['config']['uris']['app'])) {
        $core_entry = $core['config']['uris']['app'];
    }
}
if ($core_entry == 'app') {
    $config['options']['pluginsPath'] = '../../../plugins/';
} else {
    $config['options']['pluginsPath'] = '../../plugins/';
}
$config['options']['userId'] = isset($user['id']) ? (int) $user['id'] : \null;
$config['options']['stalkerHost'] = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 's' : '') . '://' . (\strpos($_SERVER['HTTP_HOST'], ':') > 0 ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT']);
$config['options']['appsPackagesPath'] = '/' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_path', 'stalker_launcher_apps/');
$config['options']['stalkerApiPath'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'api/v3/';
$config['options']['stalkerAuthPath'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'auth/token.php';
$config['options']['stalkerLoaderPath'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'c/';
$config['options']['sap'] = $app_manager->getProtocol() . $app_manager->getHost() . \join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/'), 'server/api/sap.php');
$config['options']['pingTimeout'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('watchdog_timeout', 120) * 1000;
$config['options']['themePath'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'server/api/theme_css.php?uid=' . (isset($user['id']) ? $user['id'] : '') . '&resolution={screen_height}&_=' . \time();
$available_modules = \array_values(\array_diff($all_modules, $disabled_modules));
$themes = $app_manager->getInstalledApps('theme');
if (!empty($themes)) {
    $user_theme = isset($user['theme']) ? $user['theme'] : '';
    if (!$user_theme) {
        $default_theme = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template');
        if ($default_theme == 'smart_launcher') {
            $default_theme = $themes[0]['alias'];
        }
        $user_theme = $default_theme;
    }
    $theme_alias = \str_replace('smart_launcher:', '', $user_theme);
    if ($user_theme != 'smart_launcher' && $theme_alias) {
        foreach ($themes as $theme) {
            if ($theme['alias'] == $theme_alias) {
                $config['themes'][$theme['alias']] = $theme['current_version'];
            }
        }
    }
    if (empty($config['themes'])) {
        $theme = \reset($themes);
        $config['themes'][$theme['alias']] = $theme['current_version'];
    }
}
foreach ($themes as $theme) {
    $config['themes'][$theme['alias']] = $theme['current_version'];
}
$user_apps = [];
$system_apps = $app_manager->getSystemApps();
$installed_apps = \array_merge($system_apps, $installed_apps);
foreach ($installed_apps as $app) {
    if ((!\in_array('launcher_' . $app['alias'], $available_modules) || empty($user)) && $app['type'] == 'app') {
        continue;
    }
    if ($app['type'] == 'core') {
        continue;
    }
    if ($app['config']) {
        $app_config = \json_decode($app['config'], \true);
        if ($app_config) {
            $app['config'] = $app_config;
        }
    }
    if ($app['config']) {
        $app['config']['packageName'] = $app['url'];
        $app['config']['version'] = $app['current_version'];
        if (!isset($app['config']['uris'])) {
            $app['config']['entry'] = isset($app['config']['entry']) ? $app['config']['entry'] : 'app/';
            $app['config']['url'] = $app_manager->getLauncherRootWebPath() . '/' . \join_paths($app['alias'], $app['config']['version'], $app['config']['entry']);
        }
        if ($app['options'] && ($options = \json_decode($app['options'], 1))) {
            $app['config']['options'] = $options;
        }
        try {
            $app['config']['dependencies'] = $app_manager->getFullAppDependencies($app['id']);
        } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47($e->getMessage());
            continue;
        }
        if ($app['localization']) {
            $app['localization'] = \json_decode($app['localization'], \true);
            $app['config']['name'] = isset($app['localization'][$language]['name']) ? $app['localization'][$language]['name'] : $app['config']['name'];
            $app['config']['description'] = isset($app['localization'][$language]['description']) ? $app['localization'][$language]['description'] : $app['config']['description'];
        }
        $user_apps[] = $app['config'];
    }
}
$config['apps'] = $user_apps;
\header('Content-Type: application/json');
echo \json_encode($config, 192);

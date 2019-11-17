<?php

require __DIR__ . '/common.php';
use Ministra\Lib\AppsManager;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\User;
if (!isset($_GET['key']) || !isset($_GET['mac']) || !isset($_GET['uid']) || !isset($_GET['type'])) {
    return \false;
}
$mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($_GET['key']);
if (!$mac || $mac != $_GET['mac']) {
    return \false;
}
$apps = new \Ministra\Lib\AppsManager();
$external_apps = $apps->getList(\true);
$installed_apps = \array_values(\array_filter($external_apps, function ($app) {
    return $app['installed'] == 1 && $app['status'] == 1 && !empty($app['alias']);
}));
$installed_apps = \array_map(function ($app) {
    return 'external_' . $app['alias'];
}, $installed_apps);
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
    $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
    $user_enabled_modules = $user->getServicesByType('module');
    if ($user_enabled_modules === \null) {
        $user_enabled_modules = [];
    }
    if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_modules_order_by_package', \false)) {
        $static_modules = \array_diff(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('all_modules'), $user_enabled_modules);
        $all_modules = \array_merge($static_modules, $user_enabled_modules);
    } else {
        $flipped_installed_apps = \array_flip($installed_apps);
        $installed_apps = \array_values(\array_filter($user_enabled_modules, function ($module) use($flipped_installed_apps) {
            return isset($flipped_installed_apps[$module]);
        }));
        $all_modules = \array_merge(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('all_modules'), $installed_apps);
    }
} else {
    $all_modules = \array_merge(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('all_modules'), $installed_apps);
}
$disabled_modules = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::d7814074e003e3e6aea2e49c0c79a49d((int) $_GET['uid']);
$available_modules = \array_diff($all_modules, $disabled_modules);
$dependencies = ['tv' => ['tv', 'tv_archive', 'time_shift', 'time_shift_local', 'epg.reminder', 'epg.recorder', 'epg', 'epg.simple', 'downloads_dialog', 'downloads', 'remotepvr', 'pvr_local'], 'vclub' => ['vclub', 'downloads_dialog', 'downloads']];
if (!empty($_GET['single_module'])) {
    $single_modules = \explode(',', $_GET['single_module']);
    $modules = [];
    foreach ($single_modules as $single_module) {
        if (isset($dependencies[$single_module])) {
            $modules = \array_merge($modules, $dependencies[$single_module]);
        } else {
            $modules = \array_merge($modules, [$single_module]);
        }
    }
    $available_modules = \array_intersect($modules, $available_modules);
}
if ($_GET['type'] == '.js') {
    \header('Content-Type: application/javascript');
    foreach ($available_modules as $module) {
        if (\strpos($module, 'external_') === 0) {
            $module = \str_replace('external_', '', $module);
            $module_url = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'server/api/ext_module.php?name=' . $module;
            echo \file_get_contents($module_url);
        } else {
            $file = \PROJECT_PATH . '/../c/' . $module . '.js';
            if (\file_exists($file)) {
                \readfile($file);
            }
        }
    }
} elseif (\strpos($_GET['type'], '.css') !== \false) {
    if (\preg_match('/_(\\d+)\\.css/', $_GET['type'], $match)) {
        $resolution_prefix = '_' . $match[1];
    } else {
        $resolution_prefix = '';
    }
    $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::R2015a49c88e682d6b426a39593db218e($mac);
    if (empty($user)) {
        return \false;
    }
    $theme = empty($user['theme']) || !\array_key_exists($user['theme'], \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2()) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template') : $user['theme'];
    $path = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/');
    \ob_start(function ($buffer) use($resolution_prefix, $theme, $path) {
        return \str_replace(['i' . $resolution_prefix . '/', 'i/', 'fonts/'], [$path . 'c/template/' . $theme . '/i' . $resolution_prefix . '/', $path . 'c/template/' . $theme . '/i/', $path . 'c/template/' . $theme . '/fonts/'], $buffer);
    });
    \header('Content-Type: text/css');
    foreach ($available_modules as $module) {
        if (\strpos($module, 'external_') === 0) {
            continue;
        }
        $file = \PROJECT_PATH . '/../c/template/' . $theme . '/' . $module . $resolution_prefix . '.css';
        if (\file_exists($file)) {
            \readfile($file);
        }
    }
}
\ob_end_flush();

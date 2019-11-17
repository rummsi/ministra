<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Theme;
use Ministra\Lib\User;
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3::d5de025803f2de6a57d75fa98ac892b8c('STB Api' . __FILE__);
if (empty($_GET['resolution'])) {
    exit;
}
$default_theme = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template');
if (!empty($_GET['uid'])) {
    $user = \Ministra\Lib\User::getInstance((int) $_GET['uid']);
    $default_theme = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template');
    $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
    if ($default_theme == 'smart_launcher') {
        foreach ($themes as $theme_name => $theme) {
            if (\strpos($theme_name, 'smart_launcher') === 0) {
                $default_theme = $theme_name;
                break;
            }
        }
    }
    $default_theme = $default_theme == 'smart_launcher' ? 'default' : $default_theme;
    $profile = $user->getProfile();
    if (empty($profile['theme'])) {
        $user_theme = $default_theme;
    } elseif (!\array_key_exists($profile['theme'], $themes) && $profile['theme'] != 'smart_launcher') {
        $user_theme = $default_theme;
    } elseif ($profile['theme'] == 'smart_launcher') {
        foreach ($themes as $theme_name => $theme) {
            if (\strpos($theme_name, 'smart_launcher') === 0) {
                $user_theme = $theme_name;
            }
        }
        $user_theme = $default_theme;
    } else {
        $user_theme = $profile['theme'];
    }
} else {
    $user_theme = $default_theme;
}
$user_theme = \str_replace('smart_launcher:', '', $user_theme);
$theme = new \Ministra\Lib\Theme($user_theme);
$theme->setScreenHeight((int) $_GET['resolution']);
\header('Location: ' . $theme->getCssUrl(), \true, 307);

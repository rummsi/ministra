<?php

require __DIR__ . '/config/autoload.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Silex\Application;
\set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::B735c22e927763311870c2e748ad9bd94($errno, $errstr, $errfile, $errline, $errcontext);
}, \E_ALL);
\set_exception_handler(function (\Throwable $t) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::P33f331824df667fc2c0176fa82f55c39($t);
});
$_SERVER['TARGET'] = 'ADM';
$locales = [];
$allowed_locales = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales');
foreach ($allowed_locales as $lang => $locale) {
    $locales[\substr($locale, 0, 2)] = $locale;
}
$accept_language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : \null;
if (!empty($_COOKIE['language']) && (\array_key_exists($_COOKIE['language'], $locales) || \in_array($_COOKIE['language'], $locales))) {
    $language = \substr($_COOKIE['language'], 0, 2);
} else {
    if ($accept_language && \array_key_exists(\substr($accept_language, 0, 2), $locales)) {
        $language = \substr($accept_language, 0, 2);
    } else {
        $language = \key($locales);
    }
}
$locale = $locales[$language];
if (!\headers_sent()) {
    \setcookie('debug_key', '', \time() - 3600, '/');
    \setlocale(\LC_MESSAGES, $locale);
    \setlocale(\LC_TIME, $locale);
    \putenv('LC_MESSAGES=' . $locale);
    \bindtextdomain('stb', \PROJECT_PATH . '/locale');
    \textdomain('stb');
    \bind_textdomain_codeset('stb', 'UTF-8');
}
$app = new \Silex\Application();
$app['allowed_locales'] = $allowed_locales;
$app['language'] = $language;
$app['js_validator_language'] = \in_array($language, ['pt', 'ro', 'dk', 'no', 'nl', 'cz', 'ca', 'ru', 'it', 'fr', 'de', 'se', 'en', 'pt']) ? $language : 'en';
$app['lang'] = $lang = [$language];
$app['used_locale'] = $locale;
$app['debug'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('admin_panel_debug', \false);
require __DIR__ . '/config/prod.php';
if ($app['debug']) {
    require __DIR__ . '/config/dev.php';
}
if ($app->offsetExists('test') && $app['test'] && \is_file($file = __DIR__ . '/config/test.php')) {
    require __DIR__ . '/config/test.php';
}
require __DIR__ . '/config/providers.php';
require 'controllers.php';
return $app;
